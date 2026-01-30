<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarksReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'exam_id',
        'attempt_id',
        'marks',
        'score',
        'notes',
        'created_by',
        'updated_by',
        // 'official',
        // 'published_at'
    ];

    protected $casts = [
        // 'official' => 'boolean',
        // 'published_at' => 'datetime',
        'marks' => 'float',
        'score' => 'float',
    ];

    protected static function booted()
    {
        // Triggered when a MarksReport is deleted (soft or force)
        static::deleted(function ($marksReport) {
            // Check if there is an associated attempt
            if ($marksReport->attempt_id) {
                $attempt = $marksReport->attempt;

                if ($attempt) {
                    $attempt->delete();
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attempt()
    {
        return $this->belongsTo(StudentExamAttempt::class, 'attempt_id');
    }
}
