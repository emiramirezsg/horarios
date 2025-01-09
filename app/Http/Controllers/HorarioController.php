<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Paralelo;
use App\Models\Periodo;
use Dompdf\Dompdf;
use Dompdf\Options;

class HorarioController extends Controller
{
    public function index()
    {
        // Definimos los cursos y paralelos del 1ro al 3ro
        $cursos = [
            ['curso' => '1ro', 'paralelo' => 'A'],
            ['curso' => '1ro', 'paralelo' => 'B'],
            ['curso' => '2do', 'paralelo' => 'A'],
            ['curso' => '2do', 'paralelo' => 'B'],
            ['curso' => '3ro', 'paralelo' => 'A'],
            ['curso' => '3ro', 'paralelo' => 'B'],
        ];

        // Días de la semana y periodos
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $periodos = 4; // 4 periodos por día

        // Inicializar la matriz
        $horarios = [];

        // Crear la cabecera de la matriz
        $horarios[0][0] = 'Curso y Paralelo';
        $colIndex = 1;

        // Asignar días a la primera fila
        foreach ($dias as $dia) {
            for ($periodo = 1; $periodo <= $periodos; $periodo++) {
                $horarios[0][$colIndex] = $dia . " - " . $this->numeroALetra($periodo); // Mostrar "1er periodo"
                $colIndex++;
            }
        }

        // Rellenar la primera columna con cursos y paralelos
        $rowIndex = 1;
        foreach ($cursos as $curso) {
            $horarios[$rowIndex][0] = $curso['curso'] . ' ' . $curso['paralelo'];
            for ($i = 1; $i <= $periodos * count($dias); $i++) {
                // Inicialmente, no hay materias asignadas
                $horarios[$rowIndex][$i] = '';
            }
            $rowIndex++;
        }

        // Definir las materias y docentes
        $materias = [
            'Matemáticas' => [
                ['docente' => 'Prof. Juan Pérez'],
                ['docente' => 'Prof. Ana López']
            ],
            'Sociales' => [
                ['docente' => 'Prof. Carlos Gómez'],
                ['docente' => 'Prof. Teresa Sánchez'],
                ['docente' => 'Prof. Luis Torres']
            ],
            'Artes Plásticas' => [
                ['docente' => 'Prof. Javier Martínez'],
            ],
        ];

        // Definir horas por materia
        $horasPorMateria = [
            'Matemáticas' => 5,
            'Sociales' => 6,
            'Artes Plásticas' => 4,
        ];

        // Asignar materias a los horarios
        foreach ($horarios as $rowIndex => &$row) {
            if ($rowIndex === 0) continue; // Saltar la fila de cabecera

            // Asignar materias a cada periodo
            foreach ($horasPorMateria as $materia => $horas) {
                $horasAsignadas = 0;
                $intentos = 0; // Contador de intentos

                while ($horasAsignadas < $horas && $intentos < 100) { // Limitar a 100 intentos
                    $diaIndex = array_rand($dias);
                    $periodo = rand(1, $periodos);

                    $key = $diaIndex * $periodos + $periodo; // Calcular la clave de la matriz

                    if (empty($row[$key])) {
                        // Filtrar docentes según la materia
                        $docentesDisponibles = [];

                        foreach ($materias[$materia] as $docente) {
                            $docenteEnUso = false;

                            foreach ($horarios as $compareRowIndex => $compareRow) {
                                if ($compareRowIndex === $rowIndex) continue; // Ignorar la misma fila
                                if ($compareRow[$key] !== '' && strpos($compareRow[$key], $docente['docente']) !== false) {
                                    $docenteEnUso = true;
                                    break;
                                }
                            }

                            if (!$docenteEnUso) {
                                $docentesDisponibles[] = $docente;
                            }
                        }

                        // Seleccionar un docente disponible
                        if (!empty($docentesDisponibles)) {
                            $docenteSeleccionado = $docentesDisponibles[array_rand($docentesDisponibles)];
                            $row[$key] = $materia . " (" . $docenteSeleccionado['docente'] . ")";
                            $horasAsignadas++;
                        }
                    }
                    $intentos++; // Incrementar el contador de intentos
                }
            }
        }

        // Pasar la matriz a la vista
        return view('horarios.index', compact('horarios'));
    }

    // Función para convertir números a su representación ordinal
    private function numeroALetra($numero)
    {
        switch ($numero) {
            case 1:
                return "1er periodo";
            case 2:
                return "2do periodo";
            case 3:
                return "3er periodo";
            case 4:
                return "4to periodo";
            default:
                return $numero . " periodo";
        }
    }
    public function exportarHorarios()
    {
        $horarios = Horario::with('paralelo.curso', 'docente.materia')->get();

        $html = view('horarios.pdf', compact('horarios'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream('horarios.pdf', ['Attachment' => true]);
    }

    public function generar()
    {
        $dias = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado');
        $paralelos = Paralelo::get();
        $horarios = Horario::get();

        foreach($dias as $dia){
            echo("<br> " . $dia. " |<br> ") ;
            foreach($paralelos as $paralelo){
                echo("<br>paralelo: ". $paralelo->id." materia: ");

                $materias = Materia::join('materia_curso', 'materia_curso.materia_id', '=', 'materias.id')
                    ->where('materia_curso.curso_id', $paralelo->curso_id)->get();
                //dd(Materia::join('materia_curso', 'materia_curso.materia_id', '=', 'materias.id')
                //->where('materia_curso.curso_id', $paralelo->curso_id)->toSql());

                foreach($materias as $materia){
                    echo($materia->id.", ");

                    $horas_semana = $materia->horas_semana;
                    $cont_horas_continuas = 0;

                    $periodos_materia = Periodo::where('materia_id', $materia->id)
                        ->where('paralelo_id', $paralelo->id)
                        ->get();
                    
                    $periodos_paralelo = Periodo::where('dia', $dia)
                        ->where('paralelo_id', $paralelo->id)
                        ->get();

                    if(count($periodos_materia) >= $horas_semana ){
                        continue;                        
                    }

                    if(count($periodos_paralelo) >= 7){
                        continue;                        
                    }

                    while($horas_semana > 0 && $cont_horas_continuas < 3 && count($periodos_materia) <= $horas_semana && count($periodos_paralelo) <= 7){
                        
                        foreach($horarios as $ind=>$horario){
                            
                            $hora_periodo = Periodo::where('dia', $dia)
                                ->where('docente_id', $materia->docente_id)
                                ->where('horario_id', $horario->id)
                                ->get();
                            
                            if(count($hora_periodo)==0 && $cont_horas_continuas<3){
                                $hora_periodo = Periodo::create([
                                    'dia' => $dia,
                                    'horario_id' => $horario->id,
                                    'docente_id' => $materia->docente_id,
                                    'curso_id' => $paralelo->curso_id,
                                    'paralelo_id' => $paralelo->id,
                                    'materia_id' => $materia->id
                                ]);
                                $cont_horas_continuas++;
                                $periodos_paralelo = Periodo::where('dia', $dia)
                                ->where('paralelo_id', $paralelo->id)
                                ->get();
                            }else{
                                continue;
                            }
                        }
                        $horas_semana = $horas_semana - $cont_horas_continuas;
                        //dd($horas_semana);
                        $periodos_paralelo = Periodo::where('dia', $dia)
                        ->where('paralelo_id', $paralelo->id)
                        ->get();

                        break;
                        
                    }

                }
                
            }
        }
        //dd($dias);

    }
}
