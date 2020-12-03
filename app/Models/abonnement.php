<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class abonnement extends Model
{
    use HasFactory;
    protected $fillable = [
        'intitule',
        'periode',
        'frequence',
        'montant'
    ];
}
