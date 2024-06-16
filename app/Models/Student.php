<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'number', 'course'];

    public function courseRef(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course', 'abbreviation');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'students_disciplines',
            'students_id',
            'discipline_id'
        );
    }
}
