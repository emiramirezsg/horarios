
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Periodo</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos para el formulario de creación */
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
        }

        .form-container h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 1em;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-submit {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Crear Periodo</h2>
        <form action="{{ route('periodos.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>
            </div>
            <div class="form-group">
                <label for="hora_fin">Hora de Fin</label>
                <input type="time" id="hora_fin" name="hora_fin" required>
            </div>
            <div class="form-group">
                <label for="dia">Día</label>
                <select id="dia" name="dia" required>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</body>
</html>