<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
        'status_code',
        'message',
    ];

}
