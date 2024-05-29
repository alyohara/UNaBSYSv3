@extends('layouts.app-master-export')
<style>
    /* Custom height for Select2 */
    .select2-selection--single {
        padding-bottom: 30px !important;
    }

</style>


@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light p-1 rounded">
        @auth
            <h1>Exportación</h1>

            <div class="table-responsive  nowrap" style="width:100%">
                <div class="mt-5">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Filtros Avanzados
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true" style="font-size: 0.8em;">
                        <div class="modal-dialog" style="max-width: 90% !important">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Filtros Avanzados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <form id="advanced-filter-form" action="{{ route('cargo.filtros') }}" method="post">
                                    @csrf <!-- {{ csrf_field() }} -->

                                    <div class="modal-body">

                                        <div class="row mb-3">

                                            <div class="form-group mb-3 col">
                                                <input type="text" class="form-control" id="apellido" name="apellido"
                                                       value="{{ isset($search->apellido) ? $search->apellido : '' }}">
                                                <label for="apellido">Apellido</label>
                                            </div>
                                            <div class="form-group mb-3 col">
                                                <input type="text" class="form-control" id="nombre" name="nombre"
                                                       value="{{ isset($search->nombre) ? $search->nombre : '' }}">
                                                <label for="nombre">Nombre</label>
                                            </div>
                                            <div class="form-group mb-3 col">
                                                <select class="form-control" name="materia_id" autofocus
                                                        id="materia_id">
                                                    <option value="" disabled selected>Seleccione</option>
                                                    @foreach($materias as $subject)
                                                        <option value="{{ $subject->id }}"
                                                                @if (isset($search)  && $search['materia_id'] == $subject->id) selected @endif
                                                        >{{$subject->code.' - '.$subject->name}}</option>
                                                    @endforeach

                                                </select>
                                                <label for="floatingName">Materia</label>
                                            </div>

                                        </div>
                                        <div class="row mb-3">
                                            <div class="form-group  mb-3 col">
                                                <select class="form-control" name="tipo" autofocus>
                                                    <option value="" disabled selected>Seleccione</option>
                                                    <option value="Ordinario/Concursado"
                                                            @if (isset($search) && $search['tipo'] == 'Ordinario/Concursado') selected @endif >
                                                        Ordinario/Concursado
                                                    </option>
                                                    <option value="Interino"
                                                            @if (isset($search) && $search['tipo'] == 'Interino') selected @endif>
                                                        Interino
                                                    </option>
                                                    <option value="Contratado"
                                                            @if (isset($search) && $search['tipo'] == 'Contratado') selected @endif>
                                                        Contratado
                                                    </option>
                                                </select>
                                                <label for="floatingName">Tipo</label>

                                            </div>
                                            <div class="form-group mb-3 col">
                                                <select class="form-control" name="categoria" autofocus>
                                                    <option value="" disabled selected>Seleccione</option>
                                                    <option value="Profesor Titular (TIT)"
                                                            @if (isset($search) && $search['categoria'] == 'Profesor Titular (TIT)') selected @endif>
                                                        Profesor
                                                        Titular (TIT)
                                                    </option>
                                                    <option value="Profesor Asociado (ASO)"
                                                            @if (isset($search) && $search['categoria'] == 'Profesor Asociado (ASO)') selected @endif>
                                                        Profesor
                                                        Asociado (ASO)
                                                    </option>
                                                    <option value="Profesor Adjunto (ADJ)"
                                                            @if (isset($search) && $search['categoria'] == 'Profesor Adjunto (ADJ)') selected @endif>
                                                        Profesor
                                                        Adjunto (ADJ)
                                                    </option>
                                                    <option value="Auxiliar Jefe de Trabajos Prácticos (JTP)"
                                                            @if (isset($search) && $search['categoria'] == 'Auxiliar Jefe de Trabajos Prácticos (JTP)') selected @endif>
                                                        Auxiliar Jefe de Trabajos
                                                        Prácticos (JTP)
                                                    </option>
                                                    <option value="Auxiliar Ayudante (AuxA) "
                                                            @if (isset($search) && $search['categoria'] == 'Auxiliar Ayudante (AuxA)') selected @endif>
                                                        Auxiliar
                                                        Ayudante (AuxA)
                                                    </option>
                                                    <option value="Auxiliar Ayudante Alumno (AuxAA)"
                                                            @if (isset($search) && $search['categoria'] == 'Auxiliar Ayudante Alumno (AuxAA)') selected @endif>
                                                        Auxiliar Ayudante Alumno (AuxAA)
                                                    </option>


                                                </select>
                                                <label for="floatingName">Categoria</label>
                                            </div>
                                            <div class="form-group mb-3 col">
                                                <select class="form-control" name="dedicacion_horaria" autofocus>
                                                    <option value="" disabled selected>Seleccione</option>
                                                    <option value="Dedicación Exclusiva"
                                                            @if (isset($search) && $search['dedicacion_horaria'] == 'Dedicación Exclusiva') selected @endif>
                                                        Dedicación
                                                        Exclusiva
                                                    </option>
                                                    <option value="Dedicación Semi Exclusiva"
                                                            @if (isset($search) && $search['dedicacion_horaria'] == 'Dedicación Semi Exclusiva') selected @endif>
                                                        Dedicación Semi Exclusiva
                                                    </option>
                                                    <option value="Dedicación Simple"
                                                            @if (isset($search) && $search['dedicacion_horaria'] == 'Dedicación Simple') selected @endif>
                                                        Dedicación
                                                        Simple
                                                    </option>
                                                    <option value="Ad Honorem"
                                                            @if (isset($search) && $search['dedicacion_horaria'] == 'Ad Honorem') selected @endif>
                                                        Ad
                                                        Honorem
                                                    </option>
                                                </select>
                                                <label for="floatingName">Dedicación Horaria</label>

                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="h5 pb-2 mb-4 text-success border-bottom border-success">
                                                Período
                                            </div>
                                        </div>
                                        <div class="row mb-3" style="margin-top: -20px">
                                            <div class="col">
                                                <div class="form-check ">
                                                    <input class="form-check-input" type="radio" name="periodo"
                                                           id="actual"
                                                           value="actual" checked onchange="toggle1()">
                                                    <label class="form-check-label" for="inlineRadio1">Período
                                                        Actual</label>
                                                </div>
                                                <div class="form-check ">
                                                    <input class="form-check-input" type="radio" name="periodo"
                                                           id="todos"
                                                           value="todos" onchange="toggle1()">
                                                    <label class="form-check-label" for="inlineRadio2">Todos los
                                                        Períodos</label>
                                                </div>
                                                <div class="form-check ">
                                                    <input class="form-check-input" type="radio" name="periodo"
                                                           id="rango"
                                                           value="rango" onchange="toggle2()">
                                                    <label class="form-check-label" for="inlineRadio3">Rango entre
                                                        Fechas</label>
                                                </div>
                                            </div>


                                            <div class="col-5" id="fecha_desde_group">
                                                <label for="fecha_desde">Fecha Desde:</label>
                                                <input type="date" class="form-control datepicker" id="fecha_desde"
                                                       name="fecha_desde" disabled>
                                            </div>
                                            <div class="col-5" id="fecha_hasta_group">
                                                <label for="fecha_hasta">Fecha Hasta:</label>
                                                <input type="date" class="form-control datepicker" id="fecha_hasta"
                                                       name="fecha_hasta" disabled>
                                            </div>

                                        </div>
                                        <div class="row mb-3">
                                            <div class="h5 pb-2 mb-4 text-success border-bottom border-success">
                                                Coordinador
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-top: -30px; text-align: center;">
                                            <select class="form-control" name="coordinador" autofocus>
                                                <option value="" disabled selected>Seleccione</option>
                                                @foreach($coords as $coord)
                                                    <option value="{{ $coord->id }}"
                                                            @if (isset($cord_id) && $cord_id == $coord->id) selected @endif>{{$coord->user->userData->name.', '.$coord->user->userData->lastname}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cerrar
                                            </button>
                                            <a href="{{ route('descargaDatos') }}" class="btn btn-success">Limpiar</a>
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                        </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card" style="position: relative; float: right; margin-bottom: 10px;">
                    <div class="card-body">

                        <h5>Filtrar por Función:</h5>
                        <div class=" btn-group" id="columnFilter" role="group"
                             aria-label="Basic checkbox toggle button group">

                            <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off" value="docencia">
                            <label class="btn btn-outline-primary" for="btncheck1">Docencia</label>

                            <input type="checkbox" class="btn-check" id="btncheck2" autocomplete="off"
                                   value="extension">
                            <label class="btn btn-outline-primary" for="btncheck2">Extensión</label>

                            <input type="checkbox" class="btn-check" id="btncheck3" autocomplete="off" value="gestion">
                            <label class="btn btn-outline-primary" for="btncheck3">Gestión</label>

                            <input type="checkbox" class="btn-check" id="btncheck4" autocomplete="off"
                                   value="investigacion">
                            <label class="btn btn-outline-primary" for="btncheck4">Investigación</label>
                        </div>
                    </div>
                </div>


                <table class="table" id="tabla">

                    <thead style="background-color: #f6f7f8 !important; margin-top: 5px">
                    <tr>
                        <th>Profesor <small style="font-size: 10px">Apellido</small></th>
                        <th><small style="font-size: 10px">Nombre</small></th>
                        <th>Materia</th>
                        <th>DNI</th>
                        <th>Condición</th>
                        <th>Categoria</th>
                        <th>Dedicación Horaria</th>
                        <th>Fecha Alta</th>
                        <th>Fecha Baja</th>
                        <th>Acto Administrativo / Designación</th>
                        <th>Función</th>
                        <th>Coordinador</th>
                        <th>Estado</th>
                    </tr>
                    <tr id="filterRow">
                        <th>Profesor <small style="font-size: 10px">Apellido</small></th>
                        <th><small style="font-size: 10px">Nombre</small></th>
                        <th>Materia</th>
                        <th>DNI</th>
                        <th>Condición</th>
                        <th>Categoria</th>
                        <th>Dedicación Horaria</th>
                        <th>Fecha Alta</th>
                        <th>Fecha Baja</th>
                        <th>Acto Administrativo / Designación</th>
                        <th>
                        </th>
                        <th>Coordinador</th>
                        <th>Estado</th>
                    </tr>

                    </thead>

                    <tfoot>
                    <tr id="filterRowFooter">
                        <th>Profesor <small style="font-size: 10px">Apellido</small></th>
                        <th><small style="font-size: 10px">Nombre</small></th>
                        <th>Materia</th>
                        <th>DNI</th>
                        <th>Condición</th>
                        <th>Categoria</th>
                        <th>Dedicación Horaria</th>
                        <th>Fecha Alta</th>
                        <th>Fecha Baja</th>
                        <th>Acto Administrativo / Designación</th>
                        <th></th>
                        <th>Coordinador</th>
                        <th>Estado</th>
                    </tr>

                    </tfoot>
                    <tbody>
                    @if (isset($cargos))

                        @foreach ($cargos as $cargo)

                            <tr>

                                <td><b>{{  ucfirst($cargo->lastname)}}</b></td>
                                <td>{{  $cargo->name}}</td>
                                <td>{{ $cargo->materia->code .' - '. $cargo->materia->name}}</td>
                                <td>{{  $cargo->doc}}</td>
                                <td>{{  $cargo->tipo}}</td>
                                <td>{{  $cargo->categoria}}</td>
                                <td>{{  $cargo->dedicacion_horaria}}</td>
                                <td>{{  $cargo->fecha_alta}}</td>
                                <td>{{  $cargo->fecha_baja}}</td>
                                <td>{{  $cargo->act_des}}</td>
                                {{--                            <td></td>--}}
                                <td>
                                    <ul>
                                        @if ($cargo->persona->Docencia == "Si")
                                            <li>Docencia</li>
                                        @endif
                                        @if ($cargo->persona->Investigacion == "Si")
                                            <li>Investigacion</li>
                                        @endif
                                        @if ($cargo->persona->Extension == "Si")
                                            <li>Extension</li>
                                        @endif
                                        @if ($cargo->persona->Gestion == "Si")
                                            <li>Gestion</li>
                                        @endif
                                    </ul>
                                </td>
                                <td>
                                        <?php
                                        $coords2 = \App\Http\Controllers\SubjectController::allCoord($cargo->materia->id);
                                        ?>
                                    @foreach ($coords2 as $coord)
                                        @if ($coord != null )
                                            <ul>
                                                <li>{{  ucfirst($coord->user->userData->lastname).', '.ucfirst($coord->user->userData->name)}}</li>
                                            </ul>
                                        @endif

                                    @endforeach

                                </td>


                                <td>@if($cargo->status == 1)
                                        @if($cargo->fecha_alta < date('Y-m-d'))
                                            @if ($cargo->fecha_baja)
                                                @if( date('Y-m-d')< $cargo->fecha_baja)
                                                    <span class="btn btn-outline-info btn-sm">&#10003;</span>
                                                @else
                                                    <span class="btn btn-outline-danger btn-sm"
                                                          href="">&#10539;</span>

                                                @endif
                                            @else
                                                <span class="btn btn-outline-info btn-sm"
                                                      href="">&#10003;</span>
                                            @endif
                                        @else
                                            <a class="btn btn-outline-danger btn-sm"
                                               href="">&#10539;</a>
                                        @endif
                                    @else
                                        <span class="btn btn-outline-danger btn-sm"
                                              href="">&#10539;</span>
                                    @endif</td>


                                </td>


                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            @include('auth.partials.copy')
            <script>
                function toggle2() {
                    document.getElementById('fecha_desde').disabled = false;
                    document.getElementById('fecha_hasta').disabled = false;
                }

                function toggle1() {
                    document.getElementById('fecha_desde').disabled = true;
                    document.getElementById('fecha_hasta').disabled = true;
                }


            </script>
        @endauth

        @guest
            <h1>¡Bienvenido!</h1>
            <div class="d-flex justify-content-center">
                <iframe width="923" height="519" src="https://www.youtube.com/embed/REnyeTuFR98"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>

        @endguest
    </div>

@endsection

