<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPhoto extends Model
{
    protected $fillable = [
        'project_id',
        'project_log_id',
        'path',
        'caption',
        'type',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function projectLog(): BelongsTo
    {
        return $this->belongsTo(ProjectLog::class, 'project_log_id');
    }
}