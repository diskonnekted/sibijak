<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $guarded = [];

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class, 'contractor_training');
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class);
    }
}
