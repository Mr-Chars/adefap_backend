<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTorneo extends Model
{
    use HasFactory;
    protected $table = 'request_torneo';


    protected $fillable = [
        'id_participant',
        'id_club',
        'centro_estudios',
        'ubigeo_centro_estudios',
        'year_estudios',
    ];
}
