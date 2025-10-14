<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'time_limit_minutes',
        'open_at',
        'close_at',
        'max_attempts',
        'distance_between_attempts',
        'questions_to_display',
        'full_mark',
        'review_allowed',
        'pass_threshold',
        'show_answers_after_close',
        'created_by',
    ];

    protected $casts = [
        'open_at' => 'datetime',
        'close_at' => 'datetime',
        'review_allowed' => 'boolean',
        'show_answers_after_close' => 'boolean',
    ];

    public function getPassThresholdAttribute($value)
    {
        if ($value === null) {
            return 50;
        }

        return (int) $value;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(StudentExamAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
