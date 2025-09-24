<?php
namespace App\Services;

use App\Models\ExamLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Centralized service to record exam lifecycle logs and run basic anomaly detection.
 */
class ExamAuditService
{
    // thresholds — tweak to your environment
    public const STARTS_PER_IP_WINDOW = 10;      // starts from same IP in WINDOW => alert
    public const STARTS_PER_IP_WINDOW_MINUTES = 10;
    public const UNIQUE_USERS_PER_IP_THRESHOLD = 6; // many different users from same IP
    public const STARTS_PER_USER_WINDOW = 5;
    public const STARTS_PER_USER_WINDOW_MINUTES = 3;

    /**
     * Record an exam action.
     *
     * @param string $action  start|autosave|submit|view_answers|admin_override
     * @param Request|null $request
     * @param array $payload extra metadata (attempt_id, exam_id, etc)
     * @return ExamLog
     */
    public static function record(string $action, ?Request $request = null, array $payload = [])
    {
        $ip = $request?->ip();
        $ua = $request?->userAgent();
        $userId = $request?->user()?->id ?? ($payload['user_id'] ?? null);

        $metadata = $payload;
        $metadata['server_time'] = now()->toIso8601String();

        // device fingerprint header optional (frontend can send it)
        if ($request && $request->header('X-Device-Fingerprint')) {
            $metadata['device_fingerprint'] = $request->header('X-Device-Fingerprint');
        }

        $log = ExamLog::create([
            'exam_attempt_id' => $payload['exam_attempt_id'] ?? null,
            'exam_id' => $payload['exam_id'] ?? null,
            'user_id' => $userId,
            'action' => $action,
            'metadata' => $metadata,
            'ip' => $ip,
            'user_agent' => $ua,
        ]);

        // run anomaly detection for start events
        if ($action === 'start') {
            self::detectStartAnomalies($log, $userId, $ip);
        }

        return $log;
    }

    /**
     * Detect suspicious activity on 'start' action.
     * - per-IP start rate
     * - per-user start rate
     * - many unique users from single IP
     */
    protected static function detectStartAnomalies(ExamLog $log, ?int $userId, ?string $ip)
    {
        try {
            if ($ip) {
                $ipKey = "exam:starts:ip:{$ip}";
                $ipCount = Cache::increment($ipKey);
                Cache::put($ipKey . ':ttl', true, now()->addMinutes(self::STARTS_PER_IP_WINDOW_MINUTES));
                if ($ipCount === 1) {
                    // enforce TTL after first increment
                    Cache::put($ipKey, $ipCount, now()->addMinutes(self::STARTS_PER_IP_WINDOW_MINUTES));
                }

                // track unique users from IP
                if ($userId) {
                    $usersKey = "exam:starts:ip:{$ip}:users";
                    $unique = Cache::get($usersKey, []);
                    if (!in_array($userId, $unique, true)) {
                        $unique[] = $userId;
                        Cache::put($usersKey, $unique, now()->addMinutes(self::STARTS_PER_IP_WINDOW_MINUTES));
                    }
                    $uniqueCount = count($unique);
                } else {
                    $uniqueCount = 0;
                }

                if ($ipCount !== false && $ipCount >= self::STARTS_PER_IP_WINDOW) {
                    // Create an activity log and tag this log with alert
                    ActivityLog::create([
                        'causer_id' => $userId,
                        'action' => 'suspicious_ip_rate',
                        'subject_type' => 'exam',
                        'subject_id' => $log->exam_id,
                        'properties' => [
                            'ip' => $ip,
                            'ip_count' => $ipCount,
                            'message' => 'High number of starts from one IP',
                        ],
                    ]);
                    // annotate the exam log
                    $log->update(['metadata' => array_merge($log->metadata ?? [], ['alert' => 'high_ip_start_rate'])]);
                }

                if (isset($uniqueCount) && $uniqueCount >= self::UNIQUE_USERS_PER_IP_THRESHOLD) {
                    ActivityLog::create([
                        'causer_id' => $userId,
                        'action' => 'suspicious_ip_multiuser',
                        'subject_type' => 'exam',
                        'subject_id' => $log->exam_id,
                        'properties' => [
                            'ip' => $ip,
                            'unique_user_count' => $uniqueCount,
                        ],
                    ]);
                    $log->update(['metadata' => array_merge($log->metadata ?? [], ['alert' => 'multi_user_ip'])]);
                }
            }

            if ($userId) {
                $uKey = "exam:starts:user:{$userId}";
                $uCount = Cache::increment($uKey);
                if ($uCount === 1) {
                    Cache::put($uKey, $uCount, now()->addMinutes(self::STARTS_PER_USER_WINDOW_MINUTES));
                } else {
                    Cache::put($uKey, $uCount, now()->addMinutes(self::STARTS_PER_USER_WINDOW_MINUTES));
                }

                if ($uCount !== false && $uCount >= self::STARTS_PER_USER_WINDOW) {
                    ActivityLog::create([
                        'causer_id' => $userId,
                        'action' => 'suspicious_user_rate',
                        'subject_type' => 'exam',
                        'subject_id' => $log->exam_id,
                        'properties' => [
                            'user_id' => $userId,
                            'count' => $uCount,
                            'message' => 'User started many attempts in short time',
                        ],
                    ]);
                    $log->update(['metadata' => array_merge($log->metadata ?? [], ['alert' => 'high_user_start_rate'])]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("ExamAuditService anomaly detection error: " . $e->getMessage());
        }
    }
}
