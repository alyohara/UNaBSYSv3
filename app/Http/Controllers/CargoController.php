<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargoRequest;
use App\Models\Alerta;
use App\Models\Career;
use App\Models\Cargo;
use App\Models\College;
use App\Models\Coordinador;
use App\Models\CoordinadorCarrera;
use App\Models\CoordinadorMateria;
use App\Models\Cuatrimestre;
use App\Models\Persona;
use App\Models\Subject;
use App\Models\SubjectCareer;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CargoRequest $request)
    {
        DB::beginTransaction();
        try {
            $cargo = Cargo::create($request->validated());
            $cargo->persona_id = $request->input('persona_id');
            $cargo->subject_id = $request->input('subject_id');
            $cargo->tipo = $request->input('tipo');
            $cargo->fecha_alta = $request->input('fecha_alta');
            $cargo->fecha_baja = $request->input('fecha_baja');
            $cargo->act_des = $request->input('act_des');
            $cargo->usuario_alta = auth()->user()->id;

            $cargo->status = 1;

            if ($cargo->act_des == "") {
                $cargo->act_des = "Pendiente de Carga";
                $cargo->status = 2;
            } else {
                if (auth()->user()->userData->hasRole('coordinador') && (auth()->user()->userData->hasRole(!'acaUno') || auth()->user()->userData->hasRole('admin'))) {
                    $cargo->status = 3;
                }
            }
            $cargo->categoria = $request->input('categoria');
            $cargo->dedicacion_horaria = $request->input('dedicacion_horaria');


            $cargo->save();

            $alerta = new Alerta();
            $alerta->status = 2;
            $alerta->usuario_alta = auth()->user()->id;

            if ($request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') >= date('Y-m-d') || $request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') == null) {
                if ($cargo->act_des == "Pendiente de Carga") {
                    $alerta->titulo = 'Alta de nuevo Cargo - Pendiente de Carga';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero falta cargar el acto de designación o resolución';
                    $cargo->status = 2;
                    $cargo->save();

                } else {
                    $alerta->titulo = 'Alta de nuevo Cargo';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo';
                }

            } else {
                if ($cargo->act_des == "Pendiente de Carga") {
                    $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas - Pendiente de Carga';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo y falta cargar el acto de designación o resolución';
                    $cargo->status = 2;
                    $cargo->save();
                } else {
                    $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo';
                    $cargo->status = 0;
                    $cargo->save();
                }
            }

            $alerta->tipo = 1;
            $alerta->origen = 2;
            $alerta->user_id = Auth::user()->id;
            $alerta->cargo_id = $cargo->id;
            $alerta->materia_id = $cargo->subject_id;
            $alerta->save();


            //si el formulario trae un campo cargo_id, es porque se está renovando
            // un cargo y preciso agregar los datos del cargo original de ese id; y completar estos campos:
            // renovado
            //fecha_renovacion
            //cargo_anterior_id
            //persona_que_lo_renovo_id
            //fecha_validacion
            //persona_que_lo_valido_id
            //observaciones_renovacion

            if ($request->input('cargo_id') != null) {
                $cargo->cargo_anterior_id = $request->input('cargo_id');
                $cargo->renovado = 1;
                $cargo->fecha_renovacion = date('Y-m-d');
                $cargo->persona_que_lo_renovo_id = auth()->user()->id;

                if ((auth()->user()->userData->hasRole('admin') || auth()->user()->userData->hasRole('acaUno')) && $cargo->act_des != "Pendiente de Carga") {
                    $cargo->fecha_validacion = date('Y-m-d');
                    $cargo->persona_que_lo_valido_id = auth()->user()->id;
                }
                $cargo->save();
            }

            DB::commit();
            return redirect('/cargos')->with('success', "Cargo registrado correctamente.");
        } catch (Throwable $e) {
            report($e);
            DB::rollback();
            $request->session()->flash('error', 'Ha ocurrido un error!');
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeCoord(CargoRequest $request)
    {
        $validatedData = $request->validated();
        $personaId = $request->input('persona_id');
        $subjectId = $request->input('subject_id');
        $tipo = $request->input('tipo');
        $fechaAlta = $request->input('fecha_alta');
        $fechaBaja = $request->input('fecha_baja');
        $actDes = $request->input('act_des');
        $categoria = $request->input('categoria');
        $dedicacionHoraria = $request->input('dedicacion_horaria');

        DB::beginTransaction();
        try {
            $cargo = new Cargo();
            $cargo->persona_id = $personaId;
            $cargo->subject_id = $subjectId;
            $cargo->tipo = $tipo;
            $cargo->fecha_alta = $fechaAlta;
            $cargo->fecha_baja = $fechaBaja;
            $cargo->act_des = $actDes;
            $cargo->usuario_alta = auth()->user()->id;
            $cargo->status = 3;
            if ($actDes == "") {
                $cargo->act_des = "Pendiente de Carga";
                $cargo->status = 2;
            }
            $cargo->categoria = $categoria;
            $cargo->dedicacion_horaria = $dedicacionHoraria;
            $cargo->save();

            $alerta = new Alerta();
            $alerta->status = 2;
            $alerta->usuario_alta = auth()->user()->id;

            $currentDate = date('Y-m-d');
            if (($fechaAlta < $currentDate && $fechaBaja >= $currentDate) || ($fechaAlta <= $currentDate && $fechaBaja == null)) {
                if ($actDes == "Pendiente de Carga") {
                    $alerta->titulo = 'Alta de nuevo Cargo - Pendiente de Carga';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero falta cargar el acto de designación o resolución';
                    $cargo->status = 2;
                    $cargo->save();
                } else {
                    $alerta->titulo = 'Alta de nuevo Cargo';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo';
                }
            } else {
                if ($actDes == "Pendiente de Carga") {
                    $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas - Pendiente de Carga';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo y falta cargar el acto de designación o resolución';
                    $cargo->status = 2;
                    $cargo->save();
                } else {
                    $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas';
                    $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo';
                    $cargo->status = 0;
                    $cargo->save();
                }
            }

            $alerta->tipo = 1;
            $alerta->origen = 2;
            $alerta->user_id = Auth::user()->id;
            $alerta->cargo_id = $cargo->id;
            $alerta->materia_id = $cargo->subject_id;
            $alerta->save();

            //si el formulario trae un campo cargo_id, es porque se está renovando
            // un cargo y preciso agregar los datos del cargo original de ese id; y completar estos campos:
            // renovado
            //fecha_renovacion
            //cargo_anterior_id
            //persona_que_lo_renovo_id
            if ($request->input('cargo_id') != null) {
                $cargo->cargo_anterior_id = $request->input('cargo_id');
                $cargo->renovado = 1;
                $cargo->fecha_renovacion = date('Y-m-d');
                $cargo->persona_que_lo_renovo_id = auth()->user()->id;
                $cargo->save();
            }

            DB::commit();
            return redirect('/cargosCoordinados')->with('success', "Cargo registrado correctamente.");
        } catch (Throwable $e) {
            report($e);
            DB::rollback();
            $request->session()->flash('error', 'Ha ocurrido un error!');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public function show(Cargo $cargo)
    {
        $colleges = College::all()->where('deleted_at', null);
        //$profesors = Persona::all();
        $profesors = Persona::whereHas('roles', function ($q) {
            $q->where('roles.name', '=', 'profesor');
        })->where('deleted_at', null)->get();
        $subjects = Subject::all()->where('deleted_at', null);
        return view('auth.cargos.register', ['colleges' => $colleges, 'profesors' => $profesors, 'subjects' => $subjects]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public function edit(Cargo $cargo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public function update(CargoRequest $request, Cargo $cargo)
    {
        if ($request->validated()) {

            $cargo = Cargo::find($request->input('cargo_id'));
            $cargo->persona_id = $request->input('persona_id');
            $cargo->subject_id = $request->input('subject_id');
            $cargo->tipo = $request->input('tipo');
            $cargo->fecha_alta = $request->input('fecha_alta');
            $cargo->fecha_baja = $request->input('fecha_baja');
            $cargo->act_des = $request->input('act_des');
            $cargo->categoria = $request->input('categoria');
            $cargo->dedicacion_horaria = $request->input('dedicacion_horaria');
            $cargo->usuario_modificacion = auth()->user()->id;
            $cargo->updated_at = now();
            $status_original = $cargo->status;
            if ($cargo->act_des == "" || $cargo->act_des == "Pendiente de Carga") {
                $cargo->act_des = "Pendiente de Carga";
            }
            if ($cargo->status == 2 || ($cargo->status == 3 && $cargo->act_des != "Pendiente de Carga")) {
                $cargo->status = 1;
            }


            DB::beginTransaction();
            try {
                $cargo->save();
                $alerta = new Alerta();
                $alerta->status = 2;
                $alerta->usuario_alta = auth()->user()->id;
                if ($request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') >= date('Y-m-d') || $request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') == null) {
                    if ($cargo->status == 2) {
                        $alerta->titulo = 'Modificación de Cargo - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha modificado un cargo, pero falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();

                    } else {
                        $alerta->titulo = 'Modificación de Cargo';
                        $alerta->descripcion = 'Se ha modificado un cargo';

                        if (auth()->user()->userData->hasRole('coordinador') && (auth()->user()->userData->hasRole(!'acaUno') || auth()->user()->userData->hasRole('admin'))) {
                            $cargo->status = 3;
                        } else {
                            $cargo->status = 1;
                            if ($status_original == 3) {
                                $alerta->descripcion = 'Se ha modificado y validado un cargo';
                                // guardar datos del cargo: fecha_validacion
                                // persona_que_lo_valido_id
                                $cargo->fecha_validacion = date('Y-m-d');
                                $cargo->persona_que_lo_valido_id = auth()->user()->userData->id;
                            }
                        }

                        $cargo->save();
                    }
                } else {
                    if ($cargo->act_des == "Pendiente de Carga") {
                        $alerta->titulo = 'Modificación de Cargo fuera de rango de fechas - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha modificado un cargo, pero fuera de rango de fechas de alta y baja del mismo y falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();
                    } else {
                        $alerta->titulo = 'Modificación de Cargo fuera de rango de fechas';
                        $alerta->descripcion = 'Se ha modificado un cargo, pero fuera de rango de fechas de alta y baja del mismo';
                        $cargo->status = 0;
                        $cargo->fecha_validacion = date('Y-m-d');
                        $cargo->persona_que_lo_valido_id = auth()->user()->userData->id;
                        $cargo->save();
                    }
                }

                $alerta->tipo = 3;
                $alerta->origen = 2;
                $alerta->user_id = Auth::user()->id;
                $alerta->cargo_id = $cargo->id;
                $alerta->materia_id = $cargo->subject_id;
                $alerta->save();
                DB::commit();
                return redirect('/cargos')->with('success', "Cargo Editado correctamente.");
            } catch
            (Throwable $e) {
                report($e);
                DB::rollback();
                $request->session()->flash('error', 'Ha ocurrido un error!');
            }
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public function updateCoord(CargoRequest $request, Cargo $cargo)
    {
        if ($request->validated()) {

            $cargo = Cargo::find($request->input('cargo_id'));
            $cargo->persona_id = $request->input('persona_id');
            $cargo->subject_id = $request->input('subject_id');
            $cargo->tipo = $request->input('tipo');
            $cargo->fecha_alta = $request->input('fecha_alta');
            $cargo->fecha_baja = $request->input('fecha_baja');
            $cargo->act_des = $request->input('act_des');
            $cargo->categoria = $request->input('categoria');
            $cargo->dedicacion_horaria = $request->input('dedicacion_horaria');
            $cargo->usuario_modificacion = auth()->user()->id;
            $cargo->updated_at = now();
            $status_original = $cargo->status;
            $cargo->status = 3;
            if ($cargo->act_des == "" || $cargo->act_des == "Pendiente de Carga") {
                $cargo->act_des = "Pendiente de Carga";
                $cargo->status = 2;
            }


            DB::beginTransaction();
            try {
                $cargo->save();
                $alerta = new Alerta();
                $alerta->status = 2;
                $alerta->usuario_alta = auth()->user()->id;

                if ($request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') >= date('Y-m-d') || $request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') == null) {
                    if ($cargo->status == 2) {
                        $alerta->titulo = 'Modificación de Cargo - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha modificado un cargo, pero falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();

                    } else {
                        $alerta->titulo = 'Modificación de Cargo';
                        $alerta->descripcion = 'Se ha modificado un cargo, falta su validación';
                        $cargo->status = 3;
                        $cargo->save();
                    }
                } else {
                    if ($cargo->act_des == "Pendiente de Carga") {
                        $alerta->titulo = 'Modificación de Cargo fuera de rango de fechas - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha modificado un cargo, pero fuera de rango de fechas de alta y baja del mismo y falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();
                    } else {
                        $alerta->titulo = 'Modificación de Cargo fuera de rango de fechas';
                        $alerta->descripcion = 'Se ha modificado un cargo, pero fuera de rango de fechas de alta y baja del mismo';
                        $cargo->status = 0;
                        $cargo->save();
                    }
                }

                $alerta->tipo = 3;
                $alerta->origen = 2;
                $alerta->user_id = Auth::user()->id;
                $alerta->cargo_id = $cargo->id;
                $alerta->materia_id = $cargo->subject_id;
                $alerta->save();
                DB::commit();
                return redirect('/cargosCoordinados')->with('success', "Cargo Editado correctamente.");
            } catch
            (Throwable $e) {
                report($e);
                DB::rollback();
                $request->session()->flash('error', 'Ha ocurrido un error!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
        $cargo->deleted_at = now();
        $cargo->usuario_baja = auth()->user()->id;
        $cargo->save();
        return redirect('/cargos')->with('success', "Cargo Eliminado correctamente.");
    }

    /**
     * Handle all cargos
     *
     */
    public
    function cargos()
    {
        $cargos = Cargo::all()->where('deleted_at', null)->sortByDesc('status');
        $title = '¡Borrar Cargo!';
        $text = "¿Estás seguro que deseas borrar este cargo?";
        confirmDelete($title, $text);

        return view('auth.cargos.cargos', ['cargos' => $cargos]);

    }

    /**
     * Handle all cargos
     *
     */
    public
    function stilo()
    {


        return view('auth.newStyle');

    }

    /**
     * Handle a cargo
     *
     */
    public
    function cargo($id)
    {
        $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();


        $subjects = Subject::all()->where('deleted_at', null);
        $profesors = Persona::whereHas('roles', function ($q) {
            $q->where('roles.name', '=', 'profesor');
        })->where('deleted_at', null)->get();


        return view('auth.cargos.cargo', [
            'cargo' => $cargo,
            'subjects' => $subjects,
            'profesors' => $profesors,
        ]);
    }

    /**
     * Handle a cargo
     *
     */
    public
    function cargoCoord($id)
    {
        $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
        $subjects = Subject::all()->where('deleted_at', null);
        $profesor = Persona::where('id', $cargo->persona_id)->where('deleted_at', null)->first();
        return view('auth.cargos.cargoCoord', [
            'cargo' => $cargo,
            'subjects' => $subjects,
            'profesor' => $profesor,
        ]);
    }

    /**
     * Handle a cargo
     *
     */
    public
    function verCargo($id)
    {
        $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
        $subject = $cargo->materia->id;


        return view('auth.cargos.verCargo', [
            'cargo' => $cargo,
            'subject' => $subject,
        ]);
    }

    /**
     * Handle a cargo Status
     *
     */
    public
    function toggle($id)
    {
        $cargo = Cargo::find($id);
        $cargo->usuario_modificacion = auth()->user()->id;
        $cargo->updated_at = now();
        $alerta = new Alerta();
        $alerta->usuario_alta = auth()->user()->id;

        if ($cargo->status == 1) {
            $cargo->status = 0;

            $alerta->tipo = 2;
            $alerta->origen = 2;
            $alerta->status = 2;
            $alerta->titulo = 'Baja de Cargo';
            $alerta->descripcion = 'Se ha dado de baja el cargo';

        } else {
            $cargo->status = 1;
            $alerta->tipo = 1;
            $alerta->titulo = 'Alta de Cargo';
            $alerta->descripcion = 'Se ha dado de alta el cargo';
            $alerta->origen = 2;
            $alerta->status = 2;
            if ($cargo->fecha_alta <= date('Y-m-d') && $cargo->fecha_baja >= date('Y-m-d') || $cargo->fecha_alta <= date('Y-m-d') && $cargo->fecha_baja == null) {
            } else {
                $alerta->titulo = 'Alta de  Cargo fuera de rango de fechas';
                $alerta->descripcion = 'Se ha dado de alta un cargo, pero fuera de rango de fechas de alta y baja del mismo';
                $cargo->status = 0;
                $cargo->save();
            }

        }


        $alerta->user_id = Auth::user()->id;
        $alerta->cargo_id = $cargo->id;
        $alerta->materia_id = $cargo->subject_id;
        $alerta->save();
        $cargo->save();
        return redirect('cargos');

    }

    public
    function toggle2($id)
    {
        $alerta = new Alerta();
        $alerta->usuario_alta = auth()->user()->id;
        $cargo = Cargo::find($id);
        $cargo->usuario_modificacion = auth()->user()->id;
        $cargo->updated_at = now();

        if ($cargo->status == 1) {
            $cargo->status = 0;
            $alerta->tipo = 2;
            $alerta->origen = 2;
            $alerta->status = 2;
            $alerta->titulo = 'Baja de Cargo';
            $alerta->descripcion = 'Se ha dado de baja el cargo';

        } else {
            $cargo->status = 1;
            $alerta->tipo = 1;
            $alerta->origen = 2;
            $alerta->status = 2;
            $alerta->titulo = 'Alta de Cargo';
            $alerta->descripcion = 'Se ha dado de alta el cargo';
            if ($cargo->fecha_alta <= date('Y-m-d') && $cargo->fecha_baja >= date('Y-m-d')
            ) {
            } else {
                $alerta->titulo = 'Alta de  Cargo fuera de rango de fechas';
                $alerta->descripcion = 'Se ha dado de alta un cargo, pero fuera de rango de fechas de alta y baja del mismo';
                $cargo->status = 0;
                $cargo->save();
            }

        }


        $alerta->user_id = Auth::user()->id;
        $alerta->cargo_id = $cargo->id;
        $alerta->materia_id = $cargo->subject_id;
        $alerta->save();
        $cargo->save();
        $user = User::where('persona_id', $cargo->persona_id)->first();

        $types = UserType::all()->sortDesc();
        $cargos = Cargo::all()->where('persona_id', $user->userData->id)->where('deleted_at', null);
        // return view('auth.verUsuario', ['user' => $user, 'types' => $types, 'cargos' => $cargos]);
        return redirect()->action([RegisterController::class, 'verUsuario'], $user->id);


    }

    /**
     * Export data to excel
     *
     */
    public
    function exportData()
    {
        $cuatrimestre_actual = Cuatrimestre::current()->first();
        if ($cuatrimestre_actual) {
            $cargos = Cargo::where(function ($query) use ($cuatrimestre_actual) {
                $query->where('fecha_alta', '>=', $cuatrimestre_actual->fecha_inicio)
                    ->orWhere(function ($query) use ($cuatrimestre_actual) {
                        $query->where('fecha_alta', '<', $cuatrimestre_actual->fecha_inicio)
                            ->where('fecha_baja', '>=', $cuatrimestre_actual->fecha_inicio);
                    })
                    ->orWhereNull('fecha_baja');
            })->join('personas', 'cargos.persona_id', '=', 'personas.id')->get();
        } else {
            $cargos = Cargo::all()->where('deleted_at', null)->join('personas', 'cargos.persona_id', '=', 'personas.id')->sortBy('persona_id');
        }
        $coords = Coordinador::all()->where('deleted_at', null);
        $coords2 = CoordinadorMateria::all();
        $materias = Subject::all()->where('deleted_at', null);
        return view('auth.cargos.exportacion', ['cargos' => $cargos, 'coords' => $coords, 'coords2' => $coords2, 'materias' => $materias]);

    }

    /**
     * Export data with filters
     *
     */
    public function filtros(Request $request){
        $apellido = request('apellido');
        $nombre = request('nombre');
        $periodo = request('periodo');
        $cuatrimestre_actual = Cuatrimestre::current()->first();

        $query = Cargo::query();

        if ($request->has('materia_id')) {
            $query->where('subject_id', $request->input('materia_id'));
        }

        if ($request->has('tipo')) {
            $query->where('tipo', 'LIKE', '%' . $request->input('tipo') . '%');
        }

        if ($request->has('categoria')) {
            $query->where('categoria', 'LIKE', '%' . $request->input('categoria') . '%');
        }

        if ($request->has('dedicacion_horaria')) {
            $query->where('dedicacion_horaria', 'LIKE', '%' . $request->input('dedicacion_horaria') . '%');
        }

        if ($periodo == 'actual') {
            $query->where(function ($query) use ($cuatrimestre_actual) {
                $query->where('fecha_alta', '>=', $cuatrimestre_actual->fecha_inicio)
                    ->orWhere(function ($query) use ($cuatrimestre_actual) {
                        $query->where('fecha_alta', '<', $cuatrimestre_actual->fecha_inicio)
                            ->where('fecha_baja', '>=', $cuatrimestre_actual->fecha_inicio);
                    })
                    ->orWhereNull('fecha_baja');
            });
        } elseif ($periodo == 'todo') {
        } elseif ($periodo == 'rango') {
            $fecha_desde = $request->input('fecha_desde');
            $fecha_hasta = $request->input('fecha_hasta');
            $query->where('fecha_alta', '>=', $fecha_desde)
                ->where('fecha_alta', '<=', $fecha_hasta)->orWhere(function ($query) use ($fecha_desde, $fecha_hasta) {
                    $query->where('fecha_alta', '<', $fecha_desde)
                        ->where('fecha_baja', '>=', $fecha_desde);
                });
        } else {
        }
        $query->join('personas', 'cargos.persona_id', '=', 'personas.id');

        if ($apellido) {
            if ($nombre) {
                $query->where('personas.name', 'LIKE', '%' . $nombre . '%')
                    ->where('personas.lastname', 'LIKE', '%' . $apellido . '%');

            }else
            $query->where('personas.lastname', 'LIKE', '%' . $apellido . '%');
        }else
        if ($nombre) {
            $query->where('personas.name', 'LIKE', '%' . $nombre . '%');
        }
        $cargos = $query->get();

        if ($request->has('coordinador')) {
            $cord_id = $request->input('coordinador');

            $cargosAux = null;
            foreach ($cargos as $key => $cargo) {
                $materia = $cargo->materia;
                $coords = SubjectController::allCoord($materia->id);
                foreach($coords as $coord){
                    if($coord->id == $cord_id){
                        $cargosAux[] = $cargo;
                    }
                }
            }
            $cargos = $cargosAux;
        } else {
            $cord_id = null;
        }
//
//        if ($cuatrimestre_actual) {
//            $cargos = Cargo::where(function ($query) use ($cuatrimestre_actual) {
//                $query->where('fecha_alta', '>=', $cuatrimestre_actual->fecha_inicio)
//                    ->orWhere(function ($query) use ($cuatrimestre_actual) {
//                        $query->where('fecha_alta', '<', $cuatrimestre_actual->fecha_inicio)
//                            ->where('fecha_baja', '>=', $cuatrimestre_actual->fecha_inicio);
//                    })
//                    ->orWhereNull('fecha_baja');
//            })->get();
//        } else {
//            $cargos = Cargo::all()->where('deleted_at', null)->sortBy('persona_id');
//        }
        $search =[
            'apellido' => $apellido,
            'nombre' => $nombre,
            'periodo' => $periodo,
            'fecha_desde' => $request->input('fecha_desde'),
            'fecha_hasta' => $request->input('fecha_hasta'),
            'materia_id' => $request->input('materia_id'),
            'tipo' => $request->input('tipo'),
            'categoria' => $request->input('categoria'),
            'dedicacion_horaria' => $request->input('dedicacion_horaria'),
        ];
        $coords = Coordinador::all()->where('deleted_at', null);
        $coords2 = CoordinadorMateria::all();
        $materias = Subject::all()->where('deleted_at', null);
        return view('auth.cargos.exportacion', ['cargos' => $cargos, 'coords' => $coords, 'coords2' => $coords2, 'materias' => $materias, 'search' => $search, 'cord_id' => $cord_id]);


    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public
    function renovarCargo($id)
    {

        $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
        $subjects = Subject::all()->where('deleted_at', null);
        $profesors = Persona::whereHas('roles', function ($q) {
            $q->where('roles.name', '=', 'profesor');
        })->where('deleted_at', null)->get();
        return view('auth.cargos.renovarCargo', [
            'cargo' => $cargo,
            'subjects' => $subjects,
            'profesors' => $profesors,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cargo $cargo
     * @return \Illuminate\Http\Response
     */
    public
    function renovarCargoCoord($id)
    {

        $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
        $subjects = Subject::all()->where('deleted_at', null);
        $profesor = Persona::where('id', $cargo->persona_id)->where('deleted_at', null)->first();
        return view('auth.cargos.renovarCargoCoord', [
            'cargo' => $cargo,
            'subjects' => $subjects,
            'profesor' => $profesor,
        ]);
    }

    public
    function renovarCargos(Request $request)
    {
        $selected = $request->input('selected');
        foreach ($selected as $id) {
            // Get the cargo with the given ID
            $cargoOriginal = Cargo::where('id', $id)->where('deleted_at', null)->first();
            // Renew the cargo
            // $cargo->renew();

            DB::beginTransaction();
            try {
                $cargo = new Cargo();
                $cargo->persona_id = $cargoOriginal->persona_id;
                $cargo->subject_id = $cargoOriginal->subject_id;
                $cargo->tipo = $cargoOriginal->tipo;
                $cargo->fecha_alta = now();
                $cargo->act_des = 'Pendiente de Carga';
                $cargo->categoria = $cargoOriginal->categoria;
                $cargo->dedicacion_horaria = $cargoOriginal->dedicacion_horaria;
                $cargo->status = 2;
                $cargo->cargo_anterior_id = $cargoOriginal->id;
                $cargo->renovado = 1;
                $cargo->fecha_renovacion = date('Y-m-d');
                $cargo->persona_que_lo_renovo_id = auth()->user()->id;
                $cargo->usuario_alta = auth()->user()->id;

                $cargo->save();

                $alerta = new Alerta();
                $alerta->status = 2;
                $alerta->usuario_alta = auth()->user()->id;

                if ($request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') >= date('Y-m-d') || $request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') == null) {
                    if ($cargo->act_des == "Pendiente de Carga") {
                        $alerta->titulo = 'Alta de nuevo Cargo - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();

                    } else {
                        $alerta->titulo = 'Alta de nuevo Cargo';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo';
                    }

                } else {
                    if ($cargo->act_des == "Pendiente de Carga") {
                        $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo y falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();
                    } else {
                        $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo';
                        $cargo->status = 0;
                        $cargo->save();
                    }
                }

                $alerta->tipo = 1;
                $alerta->origen = 2;
                $alerta->user_id = Auth::user()->id;
                $alerta->cargo_id = $cargo->id;
                $alerta->materia_id = $cargo->subject_id;
                $alerta->save();

                DB::commit();

            } catch (Throwable $e) {
                report($e);
                DB::rollback();
                $request->session()->flash('error', 'Ha ocurrido un error!');
            }
        }
        return redirect('/cargos')->with('success', "Cargos registrados correctamente.");
    }


    public
    function renovarCargosCoord(Request $request)
    {
        $selected = $request->input('selected');
        foreach ($selected as $id) {
            // Get the cargo with the given ID
            $cargoOriginal = Cargo::where('id', $id)->where('deleted_at', null)->first();
            // Renew the cargo
            // $cargo->renew();

            DB::beginTransaction();
            try {
                $cargo = new Cargo();
                $cargo->persona_id = $cargoOriginal->persona_id;
                $cargo->subject_id = $cargoOriginal->subject_id;
                $cargo->tipo = $cargoOriginal->tipo;
                $cargo->fecha_alta = now();
                $cargo->act_des = 'Pendiente de Carga';
                $cargo->categoria = $cargoOriginal->categoria;
                $cargo->dedicacion_horaria = $cargoOriginal->dedicacion_horaria;
                $cargo->status = 2;
                $cargo->cargo_anterior_id = $cargoOriginal->id;
                $cargo->renovado = 1;
                $cargo->fecha_renovacion = date('Y-m-d');
                $cargo->persona_que_lo_renovo_id = auth()->user()->id;
                $cargo->usuario_alta = auth()->user()->id;
                $cargo->save();

                $alerta = new Alerta();
                $alerta->usuario_alta = auth()->user()->id;

                $alerta->status = 2;

                if ($request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') >= date('Y-m-d') || $request->input('fecha_alta') <= date('Y-m-d') && $request->input('fecha_baja') == null) {
                    if ($cargo->act_des == "Pendiente de Carga") {
                        $alerta->titulo = 'Alta de nuevo Cargo - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();

                    } else {
                        $alerta->titulo = 'Alta de nuevo Cargo';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo';
                    }

                } else {
                    if ($cargo->act_des == "Pendiente de Carga") {
                        $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas - Pendiente de Carga';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo y falta cargar el acto de designación o resolución';
                        $cargo->status = 2;
                        $cargo->save();
                    } else {
                        $alerta->titulo = 'Alta de nuevo Cargo fuera de rango de fechas';
                        $alerta->descripcion = 'Se ha dado de alta un nuevo cargo, pero fuera de rango de fechas de alta y baja del mismo';
                        $cargo->status = 0;
                        $cargo->save();
                    }
                }

                $alerta->tipo = 1;
                $alerta->origen = 2;
                $alerta->user_id = Auth::user()->id;
                $alerta->cargo_id = $cargo->id;
                $alerta->materia_id = $cargo->subject_id;
                $alerta->save();

                DB::commit();

            } catch (Throwable $e) {
                report($e);
                DB::rollback();
                $request->session()->flash('error', 'Ha ocurrido un error!');
            }
        }
        return redirect('/cargosCoordinados')->with('success', "Cargos registrados correctamente.");
    }


    /**
     * Handle all cargos
     *
     */
    public
    function cargosCoordinador()
    {
        $coordinador = Coordinador::where('user_id', Auth::user()->id)->first();
        $cargos = $coordinador->all_cargos_by_materias()->sortByDesc('status');

        return view('auth.cargos.cargosCoordinador', ['cargos' => $cargos]);

    }

    /**
     * Handle a cargo
     *
     */
    public
    function cargoCoordinador($id)
    {
        $cargo = Cargo::find($id);
        $subjects = Subject::all();
        $profesors = Persona::whereHas('roles', function ($q) {
            $q->where('roles.name', '=', 'profesor');
        })->get();
        return view('auth.cargos.cargoCoordinador', [
            'cargo' => $cargo,
            'subjects' => $subjects,
            'profesors' => $profesors,
        ]);
    }

    //funcion cargosSinValidar
    public function cargosSinValidar()
    {
        //return all the cargos with status = 2 or status = 3

        $cargos = Cargo::whereIn('status', [2, 3])->where('deleted_at', null)->orderBy('fecha_alta')->get();

        return view('auth.cargos.cargosSinValidar', ['cargos' => $cargos]);
    }

    //funcion cargos renovados
    public function cargosRenovados()
    {
        //return all the cargos with status = 2 or status = 3

        $cargos = Cargo::where('renovado', 1)->where('deleted_at', null)->orderBy('fecha_alta')->get();


        return view('auth.cargos.cargosRenovados', ['cargos' => $cargos]);
    }

    public
    function validarCargos(Request $request)
    {
        $selected = $request->input('selected');
        foreach ($selected as $id) {
            // Get the cargo with the given ID
            $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
            // Renew the cargo
            // $cargo->renew();

            $cargo->fecha_validacion = now();
            $cargo->persona_que_lo_valido_id = auth()->user()->id;
            $cargo->status = 1;


            DB::beginTransaction();
            try {
                $cargo->save();


                $alerta = new Alerta();
                $alerta->status = 2;
                $alerta->usuario_alta = auth()->user()->id;
                $alerta->titulo = 'Cargo Validado';
                $alerta->descripcion = 'Se ha validado el cargo';
                $alerta->tipo = 1;
                $alerta->origen = 2;
                $alerta->user_id = Auth::user()->id;
                $alerta->cargo_id = $cargo->id;
                $alerta->materia_id = $cargo->subject_id;
                $alerta->save();

                DB::commit();

            } catch (Throwable $e) {
                report($e);
                DB::rollback();
                $request->session()->flash('error', 'Ha ocurrido un error!');
            }
        }
        return redirect('/cargosSinValidar')->with('success', "Cargos validados correctamente.");
    }

    //*****************************************FUNCIONES PARA EL FRONTEND****************************************************
    // Funcion para ver los cargos actuales
    public function viewCargosActuales()
    {
        if ($cuatrimestre_actual = Cuatrimestre::where('status', 1)->where('deleted_at', null)->first()) {
            $cargos = Cargo::where('status', 1)->where('deleted_at', null)->where('fecha_alta', '>=', $cuatrimestre_actual->fecha_inicio)->where('fecha_alta', '<=', $cuatrimestre_actual->fecha_fin)->get();
            return $cargos;
        } else {
            $cargos = Cargo::where('status', 1)->where('deleted_at', null)->get();
            return $cargos;
        }

    }

    // Funcion para ver los docentes de los cargos actuales
    public function viewDocentesCargosActuales()
    {
        $cargos = $this->viewCargosActuales();
        $docentes = [];
        foreach ($cargos as $cargo) {
            $docente = Persona::where('id', $cargo->persona_id)->where('deleted_at', null)->first();
            $docentes[] = $docente;
        }
        //cleaning docentes duplicateds
        $docentes = array_unique($docentes);
        return $docentes;
    }

    // Funcion para ver las materias que coordina un coordinador:
    // un coordinador puede coordinar varias materias
    // un coordinador puede coordinar varias carreras, si es así coordina cada una de las materias de esa carrera
    // un coordinador puede coordinar varias colleges, si es asi coordina todas las carreras en esa college; y al mismo tiempo coordina todas las materias de todas esas carreras

    public static function viewMateriasCoordinadas($coordinador_id)
    {
        $coordinador = Coordinador::where('id', $coordinador_id)->first();
        $materias = [];
        $coordinadores_materias = CoordinadorMateria::where('coordinador_id', $coordinador_id)->get();
        foreach ($coordinadores_materias as $coordinador_materia) {
            $materia = Subject::where('id', $coordinador_materia->materia_id)->where('deleted_at', null)->first();
            $materias[] = $materia;
        }
        // must de uniques
        $materias = array_unique($materias);
        return $materias;
    }

    public static function viewMateriasCarrerasCoordinadas($coordinador_id)
    {
        $coordinador = Coordinador::where('id', $coordinador_id)->where('deleted_at', null)->first();
        $materias = [];
        $coordinadores_carreras = CoordinadorCarrera::where('coordinador_id', $coordinador_id)->get();
        foreach ($coordinadores_carreras as $coordinador_carrera) {
            $carrera = Career::where('id', $coordinador_carrera->carrera_id)->where('deleted_at', null)->first();
            $materias_carreras = $carrera->materias;
            foreach ($materias_carreras as $materia_carrera) {
                $materia = Subject::where('id', $materia_carrera->subject_id)->where('deleted_at', null)->first();
                $materias[] = $materia_carrera;
            }
        }
        // must de uniques
        $materias = array_unique($materias);
        return $materias;
    }

    public static function viewMateriasCarrerasCollegeCoordinadas($coordinador_id)
    {
        $coordinador = Coordinador::where('id', $coordinador_id)->where('deleted_at', null)->first();
        $materias = [];
        $coordinadores_colleges = $coordinador->deptos;
        foreach ($coordinadores_colleges as $coordinador_college) {
            $college = College::where('id', $coordinador_college->college_id)->where('deleted_at', null)->first();
            $carreras_college = Career::where('college_id', $college->id)->where('deleted_at', null)->get();
            foreach ($carreras_college as $carrera_college) {
                $materias_carrera = Subject::where('college_id', $carrera_college->id)->where('deleted_at', null)->get();
                foreach ($materias_carrera as $materia_carrera) {
                    $materias[] = $materia_carrera;
                }
            }
        }
        // must de uniques
        $materias = array_unique($materias);
        return $materias;
    }

    public static function viewAllMateriasCoordinadas($coordinador_id)
    {
        $materias = [];
        $materias1 = CargoController::viewMateriasCoordinadas($coordinador_id);
        $materias2 = CargoController::viewMateriasCarrerasCoordinadas($coordinador_id);
        $materias3 = CargoController::viewMateriasCarrerasCollegeCoordinadas($coordinador_id);
        foreach ($materias1 as $materia1) {
            $materias[] = $materia1;
        }
        foreach ($materias2 as $materia2) {
            $materias[] = $materia2;
        }
        foreach ($materias3 as $materia3) {
            $materias[] = $materia3;
        }
        // must de uniques
        $materias = array_unique($materias);
        return $materias;
    }

    public static function viewAllDocentesCoordinadosViaMaterias($coordinador_id)
    {
        $materias = CargoController::viewAllMateriasCoordinadas($coordinador_id);
        $docentes = [];
        foreach ($materias as $materia) {

            //need to filter by $cuatrimestre_actual->fecha_inicio  and $cuatrimestre_actual->fecha_fin with cargo->fecha_alta and cargo->fecha_baja
            $cargos = Cargo::where('subject_id', $materia->id)->where('deleted_at', null)->get();
            foreach ($cargos as $cargo) {
                $cuatrimestre_actual = Cuatrimestre::current()->first();
                if ($cuatrimestre_actual) {
                    if (($cargo->fecha_alta >= $cuatrimestre_actual->fecha_inicio && $cargo->fecha_alta <= $cuatrimestre_actual->fecha_fin)
                        || ($cargo->fecha_alta >= $cuatrimestre_actual->fecha_inicio && $cargo->fecha_baja == null)
                        || ($cargo->fecha_alta <= $cuatrimestre_actual->fecha_inicio && $cargo->fecha_baja >= $cuatrimestre_actual->fecha_inicio)
                        || ($cargo->fecha_alta <= $cuatrimestre_actual->fecha_inicio && $cargo->fecha_baja == null)
                    ) {
                        $docente = Persona::where('id', $cargo->persona_id)->where('deleted_at', null)->first();
                        $docentes[] = $docente;
                    }
                } else {
                    $docente = Persona::where('id', $cargo->persona_id)->where('deleted_at', null)->first();
                    $docentes[] = $docente;
                }
            }

        }
        //cleaning docentes duplicateds
        $docentes = array_unique($docentes);
        return $docentes;
    }
    public function ActAdm(Request $request)
    {
        $selected = $request->input('cargosSelectedIds');
        $selected = explode(",", $selected);
        $actAdm = $request->input('actoAdminNumber');
        if ($actAdm == null) {
            return redirect('/cargos')->with('error', "Debe ingresar un número de acto administrativo.");
        }


        foreach ($selected as $id) {
            $cargo = Cargo::where('id', $id)->where('deleted_at', null)->first();
            $cargo->act_des = $actAdm;
            $alerta = new Alerta();
            $alerta->status = 2;
            $alerta->usuario_alta = auth()->user()->id;
            $alerta->titulo = 'Acto Administrativo Cargado';
            $alerta->descripcion = 'Se ha modificado y validado un cargo';
            $alerta->tipo = 1;
            $alerta->origen = 2;
            $alerta->save();
            $cargo->fecha_validacion = date('Y-m-d');
            $cargo->persona_que_lo_valido_id = auth()->user()->userData->id;
            $cargo->status = 0;
            $cargo->save();
        }
        return redirect('/cargos')->with('success', "Cargos validados correctamente.");

    }
}

