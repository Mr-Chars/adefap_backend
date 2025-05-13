<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;
    protected $table = 'participant';
    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'dni',
        'fecha_nacimiento',
        'ubigeo_nacimiento',
        'domicilio',
        'ubigeo_domicilio',
        'n_celular',
        'talla',
        'peso',
        'participantPhoto',
        'id_creator',
    ];
}
