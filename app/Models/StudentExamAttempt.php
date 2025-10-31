<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExamLog;

class StudentExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'submitted_at',
        'mark',
        'score',
        'scored_at',
        'attempt_number',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'scored_at' => 'datetime',
        'metadata' => 'array',
        'mark' => 'float',
        'score' => 'float'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentExamAnswer::class, 'attempt_id');
    }

    public function logs()
    {
        return $this->hasMany(ExamLog::class, 'exam_attempt_id');
    }

    public function marksReport()
    {
        return $this->hasOne(MarksReport::class, 'attempt_id');
    }
}
