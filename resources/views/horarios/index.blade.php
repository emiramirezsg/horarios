<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Horarios</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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


        <button class="btn btn-primary" id="generar-horarios">
            Generar Horarios
        </button>
        <button class="btn btn-success" id="exportar-horarios">Exportar a PDF</button>

        <table class="table table-striped">
    <thead>
        <tr>
            <th>Curso</th>
            <th>Paralelo</th>
            <th>Materia</th>
            <th>Docente</th>
            <th>Horario</th>
            <th>dia</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
    </div>

</body>

<script>
    document.getElementById('generar-horarios').addEventListener('click', function () {
        fetch("{{ route('generar.horarios') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = ''; // Limpiar tabla existente

                // Agregar nuevos periodos a la tabla
                data.periodos.forEach(periodo => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${periodo.paralelo.curso.nombre}</td>
                        <td>${periodo.paralelo.nombre}</td>
                        <td>${periodo.docente.materia.nombre}</td>
                        <td>${periodo.docente.nombre} ${periodo.docente.apellido}</td>
                        <td>${periodo.horario.hora_inicio} - ${periodo.horario.hora_fin}</td>
                        <td>${periodo.dia}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                console.error('Error al generar horarios:', data);
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
<script>
    document.getElementById('exportar-horarios').addEventListener('click', function () {
        window.location.href = "{{ route('exportar.horarios') }}";
    });
</script>

</html>
