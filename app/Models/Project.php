<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
