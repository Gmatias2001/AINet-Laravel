<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'abbreviation',
        'name',
        'name_pt',
        'type',
        'semesters',
        'ECTS',
        'places',
        'contact',
        'objectives',
        'objectives_pt',
    ];

    public $timestamps = false;

    protected $primaryKey = 'abbreviation';

    public $incrementing = false;

    protected $keyType = 'string';

    public function getFullNameAttribute()
    {
        return match ($this->type) {
            'Master'    => "Master's in ",
            'TESP'      => 'TeSP - ',
            default     => ''
        }
            . $this->name;
    }

    public function getFileNameAttribute()
    {
        return strtoupper(trim($this->abbreviation)) . '.png';
    }

    public function getImageExistsAttribute()
    {
        return Storage::exists("public/courses/{$this->fileName}");
    }

    public function getImageUrlAttribute()
    {
        if ($this->imageExists) {
            return asset("storage/courses/{$this->fileName}");
        } else {
            return asset("storage/courses/no_course.png");
        }
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'course', 'abbreviation');
    }

    public function disciplines(): HasMany
    {
        return $this->hasMany(Discipline::class, 'course', 'abbreviation');
    }
}
