<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'user_id',
        'action',
        'metadata',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function attempt()
    {
        return $this->belongsTo(StudentExamAttempt::class, 'exam_attempt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
