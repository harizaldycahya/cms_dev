<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HashMicro extends Model
{
    use HasFactory;

    // Specify the table name (optional if the table name matches the plural form of the model name)
    protected $table = 'hashmicro';

    // Specify the fillable fields
    protected $fillable = [
        'first_input',
        'second_input',
        'similar_per_total',
        'same_char',
        'similarity',
    ];
}