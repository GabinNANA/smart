<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class abonnement_user extends Model
{
    use HasFactory;
    protected $fillable = [
        'idabonnement',
        'iduser',
        'datedeb',
        'datefin',
        'montant',
        'etat'
    ];
}