@php use App\Models\Cuatrimestre; @endphp
@extends('layouts.app-master')



@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light rounded" style="margin-top: 10px">
        @auth
            <h1>Cargos</h1>
            <div class="table-responsive">
                <button id="completeActoAdmin" class="btn btn-outline-primary"
                        style="float: right; margin-left: 10px">Completar Acto Administrativo / Designacion
                </button>
                <form method="POST" action="{{ route('cargo.renovarCargos') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                    <button type="submit" class="btn btn-outline-success"
                            style="position: relative; float: right; margin-bottom: 10px">Renovar Cargos Seleccionados
                    </button>

                    <table class="table" id="tabla" style="vertical-align: middle;">
                        <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Profesor</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Dedicación Horaria</th>
                            <th>Fecha Alta</th>
                            <th>Fecha Baja</th>
                            <th>Acto Administrativo / Designación</th>
                            <th>Estado</th>
                            <th></th>
                            <th></th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($cargos as $cargo)

                            <tr @if ($cargo->status == 2) style="background-color: #efc8c8"
                                @elseif($cargo->status == 3) style="background-color: #cff1ff" @endif>

                                <td>{{ $cargo->materia->code .' - '. $cargo->materia->name}}</td>
                                <td><b>{{  ucfirst($cargo->persona->lastname)}}</b>, {{  $cargo->persona->name}}</td>
                                <td>{{  $cargo->tipo}}</td>
                                <td>{{  $cargo->categoria}}</td>
                                <td>{{  $cargo->dedicacion_horaria}}</td>
                                <td>{{  $cargo->fecha_alta}}</td>
                                <td>{{  $cargo->fecha_baja}}</td>
                                <td>{{  $cargo->act_des}}</td>
                                <td>
                                    @php
                                        $isAdminOrAca = auth()->user()->userData->hasRole('admin') || auth()->user()->userData->hasRole('acaUno') || auth()->user()->userData->hasRole('acaDos');
                                    @endphp
                                    @if($cargo->status == 1)
                                        @php
     // $isHabilitado must have fecha alta <= today but >= $cuatrimestre_actual->fecha_inicio && fecha_baja >= today but <= $cuatrimestre_actual->fecha_fin or fecha_baja is null
                                       $today = Carbon\Carbon::now();

                                       $isHabilitado = ($cargo->fecha_alta <= $today  && $cargo->fecha_alta >= $cuatrimestre_actual->fecha_inicio) && (!$cargo->fecha_baja || ($cargo->fecha_baja >= $today && $cargo->fecha_baja <= $cuatrimestre_actual->fecha_fin));
                                       @endphp
                                        <a class="btn btn-outline-{{ $isHabilitado ? 'info' : 'danger' }} btn-sm"
                                           href="{{ $isAdminOrAca ? route('cargo.cargoToggle', $cargo->id) : '#' }}">
                                            {{ $isHabilitado ? 'Habilitado' : 'Deshabilitado' }}

                                        </a>
                                        @if ($cargo->status == 1 && $cargo->act_des == "Pendiente de Carga")
                                            <span class="btn btn-outline-danger btn-sm" style="margin-top: 5px;">Pendiente de Carga</span>
                                        @endif
                                    @else
                                        @if ($cargo->status == 2)
                                            <a class="btn btn-outline-dark btn-sm"
                                               href="{{ $isAdminOrAca ? route('cargo.cargo', $cargo->id) : '#' }}">
                                                Pendiente de Carga
                                            </a>

                                        @elseif ($cargo->status == 3)
                                            <a class="btn btn-outline-secondary btn-sm"
                                               href="{{ $isAdminOrAca ? route('cargo.cargo', $cargo->id) : '#' }}">
                                                Pendiente de Validación
                                            </a>
                                        @else
                                            <a class="btn btn-outline-danger btn-sm"
                                               href="{{ $isAdminOrAca ? route('cargo.cargoToggle', $cargo->id) : '#' }}">
                                                Deshabilitado
                                            </a>
                                        @endif
                                    @endif


                                </td>
                                <td>
                                    <div style="display: flex; flex-flow: column">
                                        @if(auth()->user()->userData->hasRole('admin') || auth()->user()->userData->hasRole('acaUno'))
                                            <a class="btn btn-success btn-sm" style="margin-bottom: 5px"
                                               href="{{ route('cargo.cargo', $cargo->id) }}">@if($cargo->status == 2)
                                                    Cargar

                                                @elseif($cargo->status == 3)
                                                    Validar
                                                @else
                                                    Editar
                                                @endif</a>
                                            <a href="{{ route('cargo.destroy', $cargo->id) }}"
                                               class="btn btn-danger btn-sm" data-confirm-delete="true"
                                               style="margin-bottom: 5px">Borrar</a>
                                        @endif
                                        <a class="btn btn-primary btn-sm"
                                           style="align-items: center; margin-bottom: 5px"
                                           href="{{ route('cargo.verCargo', $cargo->id) }}">Ver</a>


                                        @if($cargo->status < 2)
                                            <a class="btn btn-outline-dark btn-sm" style="align-items: center"
                                               href="{{ route('cargo.renovarCargo', $cargo->id) }}">Renovar</a>
                                        @endif


                                    </div>
                                </td>
                                <td>@if ($cargo->act_des != "Pendiente de Carga")
                                        <input type="checkbox" class="form-check-input" name="selected[]"
                                               value="{{ $cargo->id }}"> <span style="font-size: smaller">Renovar</span>
                                    @endif
                                    @if ($cargo->status == 2)
                                        <input type="checkbox" class="form-check-input" name="actAdm[]"
                                               value="{{ $cargo->id }}"> <span style="font-size: smaller">Cargar Acto Administrativo</span>
                                    @endif
                                </td>


                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="actoAdminModal" tabindex="-1" role="dialog"
                 aria-labelledby="actoAdminModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="actoAdminModalLabel">Acto Administrativo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('cargo.ActAdm') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <input type="hidden" name="cargosSelectedIds" value=""/>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="actoAdminNumber" class="col-form-label">Acto Administrativo /
                                        Disposición:</label>
                                    <input type="text" class="form-control" id="actoAdminNumber" name="actoAdminNumber">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cerrar
                                </button>
                                <button type="submit" class="btn btn-primary" id="saveActoAdmin">Actualizar</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            @include('auth.partials.copy')
            <script>
                document.getElementById('completeActoAdmin').addEventListener('click', function () {
                    // Get all checked checkboxes
                    var checkboxes = document.querySelectorAll('input[name="actAdm[]"]:checked');
                    if (checkboxes.length === 0) {
                        alert('Seleccione por lo menos un cargo para completar el Acto Administrativo / Designación.');
                        return;
                    } else {

                        // Loop through all checked checkboxes
                        for (var i = 0; i < checkboxes.length; i++) {
                            // need to add to cargosSelectedIds all the value of acrgo->id that are checked
                            var cargosSelectedIds = document.querySelector('input[name="cargosSelectedIds"]');
                            if (cargosSelectedIds.value === '') {
                                cargosSelectedIds.value = checkboxes[i].value;
                            } else {
                                cargosSelectedIds.value += ',' + checkboxes[i].value;
                            }
                        }
                        $('#actoAdminModal').modal('show');

                    }


                });
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

