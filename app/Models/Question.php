<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'idtype',
        'intitule',
        'ordre',
        'type',
        'obligatoire'
    ];
    
    public function propositions() {
        return $this->hasMany(Proposition::class);
    }

}
