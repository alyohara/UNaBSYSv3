@extends('layouts.app-master-export-profesor')



@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light p-5 rounded">
        @auth
            <div class="d-flex justify-content-between">
                <h1>Docentes</h1> <h4>@if(isset($search))
                        @foreach($materias as $subject)
                            @if (isset($search)  && $search['materia_id'] == $subject->id)
                                Materia: {{$subject->code.' - '.$subject->name}}
                            @endif
                        @endforeach
                        @foreach($carreras as $carrera)
                            @if (isset($search)  && $search['carrera_id'] == $carrera->id)Carrera: {{$carrera->CodigoSIU.' - '.$carrera->DenominacionCarrera}}
                            @endif
                        @endforeach
                    @endif </h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"
                        style="float: right">
                    Búsqueda Avanzada
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true" style="font-size: 0.8em;">
                    <div class="modal-dialog" style="max-width: 90% !important">
                        <form id="advanced-filter-form" action="{{ route('persona.search') }}" method="post">

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Filtros Avanzados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                @csrf  {{ csrf_field() }}
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
                                            <input type="email" class="form-control" id="email" name="email"
                                                   value="{{ isset($search->email) ? $search->email : '' }}">
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="form-group mb-3 col">
                                            <input type="text" class="form-control" id="doc" name="doc"
                                                   value="{{ isset($search->doc) ? $search->doc : '' }}">
                                            <label for="doc">Documento</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-3">
                                        <div class="form-group mb-3 col">
                                            <select class="form-control" id="categorySelect">
                                                <option value="" disabled selected>Seleciona</option>
                                                <option value="subjects">Materias</option>
                                                <option value="careers">Carreras</option>
                                                <option value="departments">Departmentos</option>
                                            </select>
                                            <label for="categorySelect">Ámbito de búsqueda</label>
                                        </div>

                                        <div class="form-group mb-3 col" id="subjectsDiv" style="display: none;">
                                            <select class="form-control" name="materia_id" id="materia_id">
                                                <option value="" disabled selected>Selecciona Materia</option>
                                                @foreach($materias as $subject)
                                                    <option value="{{ $subject->id }}">{{$subject->code.' - '.$subject->name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="materia_id">Subject</label>
                                        </div>

                                        <div class="form-group mb-3 col" id="careersDiv" style="display: none;">
                                            <select class="form-control" name="carrera_id" id="carrera_id">
                                                <option value="" disabled selected>Selecciona Carrera</option>
                                                @foreach($carreras as $career)
                                                    <option value="{{ $career->id }}">{{ $career->CodigoSIU.' - '.$career->DenominacionCarrera }}</option>
                                                @endforeach
                                            </select>
                                            <label for="carrera_id">Carrera</label>
                                        </div>

                                        <div class="form-group mb-3 col" id="departmentsDiv" style="display: none;">
                                            <select class="form-control" name="departmento_id" id="departmento_id">
                                                <option value="" disabled selected>Selecciona Departmento</option>
                                                @foreach($departamentos as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="departmento_id">Departmento</label>
                                        </div>
                                    </div>

                                    <script>
                                        document.getElementById('categorySelect').addEventListener('change', function () {
                                            document.getElementById('subjectsDiv').style.display = 'none';
                                            document.getElementById('careersDiv').style.display = 'none';
                                            document.getElementById('departmentsDiv').style.display = 'none';

                                            if (this.value === 'subjects') {
                                                document.getElementById('subjectsDiv').style.display = 'block';
                                            } else if (this.value === 'careers') {
                                                document.getElementById('careersDiv').style.display = 'block';
                                            } else if (this.value === 'departments') {
                                                document.getElementById('departmentsDiv').style.display = 'block';
                                            }
                                        });
                                    </script>
                                    <hr>
                                    <div class="row mb-3">
                                        <div class="form-group mb-3 col">
                                            <select class="form-control" id="coordinadorSelect" name="coordinadorSelect">
                                                @foreach($coordinadores as $coordinador)
                                                    <option value="{{ $coordinador->id }}">{{ $coordinador->user->userData->lastname }}, {{ $coordinador->user->userData->name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="coordinadorSelect">Coordinador</label>
                                        </div>
                                    </div>
                                    <script>
                                        var options = $("#coordinadorSelect option");
                                        options.detach().sort(function(a,b) {
                                            var at = $(a).text();
                                            var bt = $(b).text();
                                            return (at > bt)?1:((at < bt)?-1:0);
                                        });
                                        $("#coordinadorSelect").append('<option value="" disabled selected>Seleciona</option>');
                                        options.appendTo("#coordinadorSelect");
                                        $('#coordinadorSelect').val('');
                                    </script>

                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cerrar
                                        </button>
                                        <a href="{{ route('persona.personas') }}" class="btn btn-success">Limpiar</a>
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5">

            </div>


            <div class="table-responsive">

                <table class="table" id="tabla">
                    <thead>
                    <tr>
                        <th style="width: 20px"></th>
                        <th>Apellido</th>
                        <th>Nombre</th>

                        <th>Email</th>

                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($personas as $persona)

                        <tr>
                            <td>{!! $persona->badgeNuevo() !!} </td>
                            <td>{{  $persona->lastname}} </td>


                            <td>{{  $persona->name}}</td>
                            <td>{{  $persona->email}}</td>

                            <td>
                                @if(auth()->user()->userData->hasRole('admin') || auth()->user()->userData->hasRole('acaUno'))
                                    <a class="btn btn-success btn-sm"
                                       href="{{ route('persona.persona', $persona->id) }}">Editar</a>
                                    <a href="{{ route('persona.destroy', $persona->id) }}"
                                       class="btn btn-danger btn-sm"
                                       data-confirm-delete="true">Borrar</a>

                                @endif
                                <a class="btn btn-primary btn-sm"
                                   href="{{ route('persona.verPersona', $persona->id) }}">Ver</a>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @include('auth.partials.copy')

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

