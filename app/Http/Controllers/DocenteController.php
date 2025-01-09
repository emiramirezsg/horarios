<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Categoria;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::with('materias')->get();
        $materias = Materia::all();
        $docentes = Docente::with('categoria', 'materias.cursos')->get();
        $materias = Materia::with('cursos')->get();
        //dd($docentes);
        return view('docentes.index', compact('docentes', 'materias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        $materias = Materia::with('cursos')->get();
        return view('docentes.create', compact('categorias', 'materias'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'categoria_id' => 'required|exists:categorias,id',
            'dias_libres' => 'array',
            'dias_libres.*' => 'nullable|string|max:255',
            'materia_id' => 'array',
            'materia_id.*' => 'nullable|exists:materias,id',
        ], [
            'email.unique' => 'El correo ya está en uso. Por favor, usa otro correo electrónico.',
        ]);

        try {
            // Crear el usuario en la tabla `users`
            $user = User::create([
                'name' => $validatedData['nombre'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'role' => 'docente',
            ]);

            // Crear el docente en la tabla `docentes`
            $docente = new Docente();
            $docente->nombre = $validatedData['nombre'];
            $docente->apellido = $validatedData['apellido'];
            $docente->email = $validatedData['email'];
            $docente->categoria_id = $validatedData['categoria_id'];
            $docente->user_id = $user->id;
            $docente->dias_libres = json_encode($validatedData['dias_libres']);
            // Guarda el docente en la base de datos
            if ($docente->save()) {
                // Asigna el docente a las materias seleccionadas
                foreach ($validatedData['materia_id'] as $materiaId) {
                    if ($materiaId) { // Solo si el ID de materia no está vacío
                        $materia = Materia::find($materiaId);
                        $materia->docente_id = $docente->id;
                        $materia->save();
                    }
                }

                return redirect()->route('docentes.index')->with('success', 'Docente creado correctamente');
            } else {
                $user->delete();
                return back()->with('error', 'Error al guardar el docente');
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
        {
            $docente = Docente::with('materias')->findOrFail($id);
            return view('docentes.show', compact('docente'));
        }

    public function edit(Docente $docente)
        {
            $categorias = Categoria::all();
            $materias = Materia::all();

            return view('docentes.edit', compact('docente', 'categorias', 'materias'));
        }

    public function update(Request $request, $id)
        {
            //dd($request->all());
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'email' => 'required|email|max:255',//|unique:users,email,' . $id, // Asegúrate de permitir el mismo email
                'categoria_id' => 'required|exists:categorias,id',
                'dias_libres' => 'array',
                'dias_libres.*' => 'nullable|string|max:255',
                'materia_id' => 'array',
                'materia_id.*' => 'nullable|exists:materias,id',
            ], [
                'email.unique' => 'El correo ya está en uso. Por favor, usa otro correo electrónico.',
            ]);
        
            /*
            try {
            */
                // Encuentra el usuario correspondiente
                $user = User::where('email', $validatedData['email'])->first();
                if (!$user) {
                    return back()->with('error', 'Usuario no encontrado');
                }
        
                // Actualiza el usuario en la tabla `users`
                $user->name = $validatedData['nombre'];
                $user->email = $validatedData['email'];
                // No actualizamos la contraseña aquí, pero podrías hacerlo si es necesario.
                $user->save();
        
                $docente = Docente::findOrFail($id);
                $docente->nombre = $validatedData['nombre'];
                $docente->apellido = $validatedData['apellido'];
                $docente->email = $validatedData['email'];
                $docente->categoria_id = $validatedData['categoria_id'];
                if(isset($request->dias_libres)){
                    $docente->dias_libres = json_encode($validatedData['dias_libres']);
                }else{
                    $docente->dias_libres = [];
                }
                
                if ($docente->save()) {
                    $materias_docente = Materia::where('docente_id', $docente->id)->get();
                    foreach($materias_docente as $materia_docente){
                        $materia_docente->docente_id = null;
                        $materia_docente->save();
                    }

                    foreach ($validatedData['materia_id'] as $materiaId) {
                        if ($materiaId) { 
                            $materia = Materia::find($materiaId);
                            $materia->docente_id = $docente->id;
                            $materia->save();
                        }
                    }
                    
                    return redirect()->route('docentes.index')->with('success', 'Docente actualizado correctamente');
                } else {
                    return back()->with('error', 'Error al guardar el docente');
                }
            /*
            } catch (\Exception $e) {
                return back()->with('error', 'Error: ' . $e->getMessage());
            }
            */
        }
        
        public function destroy($id)
    {
        try {
            $docente = Docente::findOrFail($id);

            if ($docente->user) { 
                $docente->user->delete();
            }
            $docente->delete();

            return redirect()->route('docentes.index')->with('success', 'Docente y usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el docente: ' . $e->getMessage());
        }
    }

    public function horarios()
    {
        $user = Auth::user();
        $docente = Docente::where('user_id', $user->id)->first();
        
        if (!$docente) {
            return redirect()->route('home')->with('error', 'No se encontró el docente asociado.');
        }

        return view('docentevista.index', compact('docente'));
    }
}
