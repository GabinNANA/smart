<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestataire extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'domaine',
        'nom',
        'telephone',
        'email',
        'adresse',
        'pays',
        'logo',
        'description',
        'isavant'
    ];
}
