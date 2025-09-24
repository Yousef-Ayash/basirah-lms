<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportsService
{
    /**
     * Per-student per-exam summary
     * returns attempts_count, avg, best, last_score, last_attempt_at
     */
    public static function perStudentExam(int $userId, int $examId)
    {
        $stats = DB::table('student_exam_attempts')
            ->selectRaw('COUNT(*) as attempts, AVG(score) as avg_score, MAX(score) as best_score, MAX(submitted_at) as last_submitted_at')
            ->where('user_id', $userId)
            ->where('exam_id', $examId)
            ->first();

        // also fetch last attempt row
        $last = DB::table('student_exam_attempts')
            ->where('user_id', $userId)
            ->where('exam_id', $examId)
            ->orderBy('submitted_at', 'desc')
            ->first();

        return [
            'attempts' => $stats->attempts ?? 0,
            'avg_score' => $stats->avg_score ? round((float) $stats->avg_score, 2) : null,
            'best_score' => $stats->best_score ? round((float) $stats->best_score, 2) : null,
            'last_score' => $last->score ?? null,
            'last_submitted_at' => $stats->last_submitted_at ?? null,
        ];
    }

    /**
     * Per-subject aggregate: for each exam under subject compute avg & pass_rate
     * pass_threshold is percentage (e.g. 50)
     */
    public static function perSubjectAggregate(int $subjectId, float $passThreshold = 50.0)
    {
        // get exam ids for the subject
        $exams = DB::table('exams')->where('subject_id', $subjectId)->pluck('id');

        if ($exams->isEmpty())
            return [];

        $placeholders = implode(',', array_fill(0, count($exams), '?'));

        $bindings = $exams->toArray();
        $bindings[] = $passThreshold;

        $sql = "
            SELECT exam_id,
                   AVG(score) as avg_score,
                   COUNT(*) as attempts,
                   SUM(CASE WHEN score >= ? THEN 1 ELSE 0 END) as passed
            FROM student_exam_attempts
            WHERE exam_id IN ({$placeholders})
            GROUP BY exam_id
        ";

        // Because bindings ordering must match, we pass passThreshold first then exam ids.
        // Rebuild properly:
        $sql = "
            SELECT exam_id,
                   AVG(score) as avg_score,
                   COUNT(*) as attempts,
                   SUM(CASE WHEN score >= ? THEN 1 ELSE 0 END) as passed
            FROM student_exam_attempts
            WHERE exam_id IN ({$placeholders})
            GROUP BY exam_id
        ";

        $bindings = array_merge([$passThreshold], $exams->toArray());

        $rows = DB::select($sql, $bindings);

        if (empty($rows)) {
            return [];
        }

        // Fetch the related exam models to get their titles
        $examIds = array_column($rows, 'exam_id');
        $examModels = \App\Models\Exam::whereIn('id', $examIds)->get()->keyBy('id');

        // map to associative keyed by exam_id
        $result = [];
        foreach ($rows as $r) {
            $result[$r->exam_id] = [
                'exam_id' => $r->exam_id,
                'exam' => $examModels->get($r->exam_id), // Attach the full exam object
                'avg_score' => $r->avg_score !== null ? round((float) $r->avg_score, 2) : null,
                'attempts' => (int) $r->attempts,
                'passed' => (int) $r->passed,
                'pass_rate' => $r->attempts ? round(((int) $r->passed / (int) $r->attempts) * 100, 2) : null,
            ];
        }

        return $result;
    }

    /**
     * Per-level summary (aggregates across subjects in a level)
     * returns metrics per subject and an overall summary
     */
    public static function perLevelSummary(int $levelId, float $passThreshold = 50.0)
    {
        $subjects = DB::table('subjects')->where('level_id', $levelId)->select('id', 'title')->get();

        $summary = [];
        foreach ($subjects as $s) {
            $agg = self::perSubjectAggregate((int) $s->id, $passThreshold);

            // aggregate across subject's exams
            $totalAttempts = 0;
            $totalPassed = 0;
            $avgScores = [];
            foreach ($agg as $examAgg) {
                $totalAttempts += $examAgg['attempts'];
                $totalPassed += $examAgg['passed'];
                if ($examAgg['avg_score'] !== null)
                    $avgScores[] = $examAgg['avg_score'];
            }

            $subjectAvg = count($avgScores) ? round(array_sum($avgScores) / count($avgScores), 2) : null;
            $passRate = $totalAttempts ? round(($totalPassed / $totalAttempts) * 100, 2) : null;

            $summary[] = [
                'subject_id' => $s->id,
                'subject_title' => $s->title,
                'avg_score' => $subjectAvg,
                'attempts' => $totalAttempts,
                'pass_rate' => $passRate,
            ];
        }

        // overall summary across level
        $overallAttempts = array_sum(array_column($summary, 'attempts'));
        $overallPassed = 0;
        foreach ($summary as $row) {
            $overallPassed += ($row['pass_rate'] && $row['attempts']) ? ($row['pass_rate'] / 100) * $row['attempts'] : 0;
        }
        $overallPassRate = $overallAttempts ? round(($overallPassed / $overallAttempts) * 100, 2) : null;

        return [
            'subjects' => $summary,
            'overall' => [
                'attempts' => $overallAttempts,
                'pass_rate' => $overallPassRate,
            ],
        ];
    }
}
