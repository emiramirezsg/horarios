<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = ['hora_inicio', 'hora_fin', 'dia',
        'paralelo_id', 'horario_id', 'docente_id', 'curso_id', 'aula_id', 'paralelo_id', 'materia_id'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
