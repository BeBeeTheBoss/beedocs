<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docs extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'description',
        'method',
        'endpoints',
        'is_authorized',
        'request',
        'response',
    ];

    public function error_responses(){
        return $this->hasMany(ErrorResponse::class , 'doc_id');
    }

}
