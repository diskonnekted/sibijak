<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    protected $guarded = [];

    protected $table = 'training_participants';

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}