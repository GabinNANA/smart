<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_reponse extends Model
{
    use HasFactory;
    protected $fillable = [
        'idusers',
        'idhabitation',
        'etat',
        'idquestion',
        'idparent',
        'reponse'
    ];
}
