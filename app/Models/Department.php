<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'abbreviation',
        'name',
        'name_pt',
    ];

    public $timestamps = false;

    protected $primaryKey = 'abbreviation';

    public $incrementing = false;

    protected $keyType = 'string';

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class, 'department', 'abbreviation');
    }
}
