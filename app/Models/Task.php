<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\User;
use App\Traits\CommonQueryScopes;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Comment;

class Task extends Model
{
    use HasFactory;
    use CommonQueryScopes;
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'status', 'due_date', 'project_id', 'assigned_to'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
