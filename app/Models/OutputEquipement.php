<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputEquipement extends Model
{
    use HasFactory;
    protected $fillable = [
        'idcategorie',
        'objet',
        'type',
        'moment_frequence',
        'personne_organisme',
        'document_completer',
        'reference'
    ];

}
