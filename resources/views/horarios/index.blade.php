<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario Escolar</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
        .paralelo {
            font-size: 12px;
            line-height: 1.2;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Horario Escolar</h1>
    <button id="generar-horarios">Generar Horarios</button>
    <table>
        <thead>
            <tr>
                <th>Día</th>
                <th>Horario</th>
            </tr>
        </thead>
        <tbody id="horario-body">
        </tbody>
    </table>
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
                const tableHead = document.querySelector('table thead tr');
                const tbody = document.getElementById('horario-body');
                tableHead.innerHTML = `
                    <th>Día</th>
                    <th>Horario</th>
                `; 
                tbody.innerHTML = ''; 
                const paralelos = data.paralelos; 
                paralelos.forEach(paralelo => {
                    const th = document.createElement('th');
                    th.textContent = `${paralelo.curso.nombre} ${paralelo.nombre}`;
                    tableHead.appendChild(th);
                });
                const dias = [...new Set(data.periodos.map(periodo => periodo.dia))];
                const horarios = data.horarios;
                dias.forEach(dia => {
                    const periodosDia = data.periodos.filter(p => p.dia === dia);
                    const horariosUnicos = [...new Set(periodosDia.map(p => p.horario.id))];
                    horariosUnicos.forEach((horarioId, index) => {
                        const horario = horarios.find(h => h.id === horarioId);
                        console.log('1');
                        console.log(horarios);
                        console.log(data.periodos);
                        console.log('2');
                        console.log(horario);
                        console.log('3');
                        console.log(horarioId);
                        console.log('4');

                        const fila = document.createElement('tr');
                        if (index === 0) {
                            const tdDia = document.createElement('td');
                            tdDia.textContent = dia;
                            tdDia.rowSpan = horariosUnicos.length;
                            fila.appendChild(tdDia);
                        }
                        const tdHorario = document.createElement('td');
                        if(horario != undefined){
                            tdHorario.textContent = `${horario.hora_inicio} - ${horario.hora_fin}`;
                        }else{
                            tdHorario.textContent = '';

                        }
                        fila.appendChild(tdHorario);
                        paralelos.forEach(paralelo => {
                            const tdParalelo = document.createElement('td');
                            const periodo = periodosDia.find(p => 
                                p.paralelo.id === paralelo.id && 
                                p.horario.id === horarioId
                            );
                            if (periodo) {
                                tdParalelo.textContent = `${periodo.docente.materia.nombre} - ${periodo.docente.nombre} ${periodo.docente.apellido}`;
                            } else {
                                tdParalelo.textContent = '';
                            }

                            fila.appendChild(tdParalelo);
                        });

                        tbody.appendChild(fila);
                    });
                });
            } else {
                console.error('Error al generar horarios:', data);
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
</html>
