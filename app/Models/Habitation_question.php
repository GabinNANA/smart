<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitation_question extends Model
{
    use HasFactory;
    protected $fillable = [
        'idhabitation',
        'idquestion'
    ];
}
