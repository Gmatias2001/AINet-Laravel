<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'course',
        'year',
        'semester',
        'abbreviation',
        'name',
        'name_pt',
        'ECTS',
        'hours',
        'optional',
    ];

    public $timestamps = false;

    public function getSemesterDescriptionAttribute()
    {
        return match ($this->semester) {
            0       => "Anual",
            1       => "1st",
            2       => "2nd",
            default => '?'
        };
    }

    public function courseRef(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course', 'abbreviation');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teachers_disciplines');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'students_disciplines',
            'discipline_id',
            'students_id'
        );
    }
}
