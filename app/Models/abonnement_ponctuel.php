<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class abonnement_ponctuel extends Model
{
    use HasFactory;
    protected $fillable = [
        'iduser_reponse',
        'montant',
    ];
}