<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroEstudios extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'ubigeo',
        'id_creator',
    ];
}
