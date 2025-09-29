<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'level_id',
        'cover_image_path',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials()
    {
        return $this->hasMany(SubjectMaterial::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
