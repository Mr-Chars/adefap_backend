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
        'id_creator',
        'id_centro_estudios',
        'id_category',
        'id_region',
    ];
}
