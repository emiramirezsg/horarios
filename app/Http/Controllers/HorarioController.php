<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
