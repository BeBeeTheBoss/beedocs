<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function docs(){
        return $this->hasOne(Docs::class, 'api_id');
    }
}
