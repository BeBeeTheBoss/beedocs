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

}
