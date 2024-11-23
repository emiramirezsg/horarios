<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Horarios</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background: url('https://www.orientacionandujar.es/wp-content/uploads/2020/08/fondos-para-clases-virtuales-1.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        /* Estilos para la tabla de horarios */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Estilos para botones */
        .botones {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
            text-align: center;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .btn-agregar-horario {
            background-color: #ffc107;
        }

        .btn-agregar-horario:hover {
            background-color: #e0a800;
        }

        .btn-regresar {
            background-color: #6c757d;
            color: #fff;
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-regresar:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="btn btn-regresar">Inicio</a>
    
    <div class="container">
        <h2>Lista de Horarios</h2>
        
        <a href="#modalAgregarHorario" class="btn btn-agregar-horario open-modal">Generar Horario</a>
        
        @if(!empty($horarios) && count($horarios) > 1)
            <table>
                <thead>
                    <tr>
                        @foreach ($horarios[0] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i < count($horarios); $i++)
                        <tr>
                            @foreach ($horarios[$i] as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        @else
            <div class="horarios">
                <div class="horario-card">
                    <p><em>No existen horarios</em></p>
                </div>
            </div>
        @endif
    </div>
    
</body>
</html>
