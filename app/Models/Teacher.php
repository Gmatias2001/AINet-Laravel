<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'department', 'office', 'extension', 'locker'];

    public function departmentRef(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department', 'abbreviation');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'teachers_disciplines'
        );
    }
}
