<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre',
        'classement',
        'standard',
        'livrable',
        'validite',
        'delai',
        'cout_etude',
        'frais_admin',
        'penalite',
        'ispayer'
    ];

}
