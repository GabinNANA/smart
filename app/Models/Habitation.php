<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitation extends Model
{
    use HasFactory;
    protected $fillable = [
        'intitule'
    ];

    public function montants() {
        return $this->hasMany(Montant::class);
    }
}
