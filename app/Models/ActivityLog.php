<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'causer_id',
        'action',
        'subject_type',
        'subject_id',
        'properties',
    ];

    protected $casts = ['properties' => 'array'];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
