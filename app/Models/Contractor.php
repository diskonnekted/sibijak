<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $guarded = [];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'contractor_training');
    }
}
