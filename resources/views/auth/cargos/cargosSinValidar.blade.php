@extends('layouts.app-master-export-validate')



@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light p-1 rounded">
        @auth
            <h1>Cargos sin Validar</h1>
            <button id="completeActoAdmin" class="btn btn-outline-primary"
                    style="float: right; margin-left: 10px">Completar Acto Administrativo / Designacion
            </button>
            <form method="POST" action="{{ route('cargo.validarCargos') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                <button type="submit" class="btn btn-outline-success"
                        style="position: relative; float: right; margin-bottom: 10px">Validar Cargos
                    Seleccionados
                </button>
                <div class="table  nowrap" style="width:100%">

                    <table class="table" id="tabla">


                        <thead style="background-color: #f6f7f8 !important; margin-top: 5px">
                        <tr>
                            <th>Profesor <small style="font-size: 10px">Apellido</small></th>
                            <th><small style="font-size: 10px">Nombre</small></th>
                            <th>Materia</th>
                            <th>Condición</th>
                            <th>Categoria</th>
                            <th>Dedicación Horaria</th>
                            <th>Fecha Alta</th>
                            <th>Acto Administrativo / Designación</th>

                            <th>Renovado Por</th>
                            <th>Estado</th>


                            <th></th>
                            <th></th>

                        </tr>
                        {{--                    <tr id="filterRow">--}}
                        {{--                        <th>Profesor <small style="font-size: 10px">Apellido</small></th>--}}
                        {{--                        <th><small style="font-size: 10px">Nombre</small></th>--}}
                        {{--                        <th>Materia</th>--}}
                        {{--                        <th>Condición</th>--}}
                        {{--                        <th>Categoria</th>--}}
                        {{--                        <th>Dedicación Horaria</th>--}}
                        {{--                        <th>Fecha Alta</th>--}}
                        {{--                        <th>Coordinador</th>--}}
                        {{--                        <th>Estado</th>--}}
                        {{--                        <th></th>--}}
                        {{--                    </tr>--}}

                        {{--                    </thead>--}}

                        {{--                    <tfoot>--}}
                        {{--                    <tr id="filterRowFooter">--}}
                        {{--                        <th>Profesor <small style="font-size: 10px">Apellido</small></th>--}}
                        {{--                        <th><small style="font-size: 10px">Nombre</small></th>--}}
                        {{--                        <th>Materia</th>--}}
                        {{--                        <th>Condición</th>--}}
                        {{--                        <th>Categoria</th>--}}
                        {{--                        <th>Dedicación Horaria</th>--}}
                        {{--                        <th>Fecha Alta</th>--}}
                        {{--                        <th>Coordinador</th>--}}
                        {{--                        <th>Estado</th>--}}
                        {{--                        <th></th>--}}
                        {{--                    </tr>--}}

                        {{--                    </tfoot>--}}
                        <tbody>
                        @foreach ($cargos as $cargo)

                            <tr @if ($cargo->status == 2) style="background-color: rgba(239,200,200,0.45)"
                                @elseif($cargo->status == 3) style="background-color: rgba(207,241,255,0.45)" @endif>

                                <td><b>{{  ucfirst($cargo->persona->lastname)}}</b></td>
                                <td>{{  $cargo->persona->name}}</td>
                                <td>{{ $cargo->materia->code .' - '. $cargo->materia->name}}</td>
                                <td>{{  $cargo->tipo}}</td>
                                <td>{{  $cargo->categoria}}</td>
                                <td>{{  $cargo->dedicacion_horaria}}</td>
                                <td>{{  $cargo->fecha_alta}}</td>
                                <td>{{ $cargo->act_des }}</td>

                                <td><b>
                                        {{$cargo->persona_que_lo_renovo ? $cargo->persona_que_lo_renovo->userData->lastname.',' : ''}}
                                    </b>
                                    {{$cargo->persona_que_lo_renovo ? $cargo->persona_que_lo_renovo->userData->name : ''}}

                                </td>


                                <td>
                                    @php
                                        $isAdminOrAca = auth()->user()->userData->hasRole('admin') || auth()->user()->userData->hasRole('acaUno') || auth()->user()->userData->hasRole('acaDos');
                                    @endphp
                                    @if($cargo->status == 1)
                                        @php
                                            $isHabilitado = $cargo->fecha_alta < date('Y-m-d') && (!$cargo->fecha_baja || date('Y-m-d') < $cargo->fecha_baja);
                                        @endphp
                                        <a class="btn btn-outline-{{ $isHabilitado ? 'info' : 'danger' }} btn-sm"
                                           href="{{ $isAdminOrAca ? route('cargo.cargoToggle', $cargo->id) : '#' }}">
                                            {{ $isHabilitado ? 'Habilitado' : 'Deshabilitado' }}
                                        </a>
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
                                <td>

                                    @if ($cargo->status == 2)
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   id="flexSwitchCheckDefault" name="actAdm[]"
                                                   value="{{ $cargo->id }}">
                                            <label class="form-check-label" for="flexSwitchCheckDefault"
                                                   style="font-size: smaller">Cargar</label>
                                        </div>

                                    @endif
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="flexSwitchCheckDefault" name="selected[]"
                                               value="{{ $cargo->id }}">
                                        <label class="form-check-label" for="flexSwitchCheckDefault"
                                               style="font-size: smaller">Validar</label>
                                    </div>

                                </td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </form>

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
                                <p><em>Recuerde que al completar la carga del acto adminsitrativo / desiganción, también lo valida</em></p>
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


