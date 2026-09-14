<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','active','base_url'];

    public function groups(){
        return $this->hasMany(Group::class);
    }

    public function viewers(){
        return $this->belongsToMany(User::class, 'project_viewers')->withTimestamps();
    }

    public function canBeViewedBy(User $user): bool {
        return $this->user_id === $user->id || $this->viewers()->where('users.id', $user->id)->exists();
    }

}
