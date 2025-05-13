<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    use HasFactory;
    protected $table = 'tb_ubigeos';
    protected $fillable = [
        'id_ubigeo',
        'ubigeo_reniec',
        'departamento',
        'provincia',
        'distrito',
    ];
}
