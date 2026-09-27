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

    public function logs()
    {
        return $this->hasMany(ProjectLog::class)->latest();
    }

    public function photos()
    {
        return $this->hasMany(ProjectPhoto::class)->latest();
    }

    public function pengawasVerifier()
    {
        return $this->belongsTo(User::class, 'pengawas_verified_by');
    }

    public function finalVerifier()
    {
        return $this->belongsTo(User::class, 'final_verified_by');
    }
}
