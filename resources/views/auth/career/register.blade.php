@extends('layouts.app-master')



@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light p-5 rounded">
        @auth
            <form method="post" action="{{ route('career.perform') }}">
                <a style="margin-bottom: 5px; float: right" href="#" class="  btn btn-secondary "
                   onclick="window.history.back();">Volver</a>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />


                <h1 class="h3 mb-3 fw-normal">Nueva Carrera </h1>




                <div class="h5 pb-2 mb-4 text-success border-bottom border-success">
                    Datos de la Carrera
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="name" value="{{old('name') }}" placeholder="Nombre" autofocus>
                    <label for="floatingName">Nombre</label>
                    @if ($errors->has('name'))
                        <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-group form-floating mb-3">
                    <input type="date" class="form-control" name="dateInit" value="{{old('dateInit') }}" placeholder="Fecha de Inicio"  autofocus>
                    <label for="floatingName">Fecha de Inicio de Actividades</label>
                    @if ($errors->has('dateInit'))
                        <span class="text-danger text-left">{{ $errors->first('dateInit') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="data" value="{{old('data') }}" placeholder="Data Extra"  autofocus>
                    <label for="floatingName">Data</label>
                    @if ($errors->has('data'))
                        <span class="text-danger text-left">{{ $errors->first('data') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="CodigoSIU" value="{{old('CodigoSIU') }}" placeholder="Codigo SIU"  autofocus required>
                    <label for="floatingName">Codigo SIU</label>
                    @if ($errors->has('CodigoSIU'))
                        <span class="text-danger text-left">{{ $errors->first('CodigoSIU') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input required type="text" class="form-control" name="DenominacionCarrera" value="{{old('DenominacionCarrera') }}" placeholder="Denominación de Carrera"  autofocus >
                    <label for="floatingName">Denominación de Carrera</label>
                    @if ($errors->has('DenominacionCarrera'))
                        <span class="text-danger text-left">{{ $errors->first('DenominacionCarrera') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="Titulo" value="{{old('Titulo') }}" placeholder="Título"  autofocus required>
                    <label for="floatingName">Título</label>
                    @if ($errors->has('Titulo'))
                        <span class="text-danger text-left">{{ $errors->first('Titulo') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="ResolucionAprobacionCS" value="{{old('ResolucionAprobacionCS') }}" placeholder="Resolución de Aprobación Consejo Superior UNAB"  autofocus>
                    <label for="floatingName">Resolución de Aprobación Consejo Superior UNAB</label>
                    @if ($errors->has('ResolucionAprobacionCS'))
                        <span class="text-danger text-left">{{ $errors->first('ResolucionAprobacionCS') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="ResolucionCorrelativasCS" value="{{old('ResolucionCorrelativasCS') }}" placeholder="Resolución de Correlativas Consejo Superior UNAB"  autofocus>
                    <label for="floatingName">Resolución de Correlativas Consejo Superior UNAB</label>
                    @if ($errors->has('ResolucionCorrelativasCS'))
                        <span class="text-danger text-left">{{ $errors->first('ResolucionCorrelativasCS') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <input type="text" class="form-control" name="ResolucionMinisterial" value="{{old('ResolucionMinisterial') }}" placeholder="Resolución Ministerial"  autofocus>
                    <label for="floatingName">Resolución Ministerial</label>
                    @if ($errors->has('ResolucionMinisterial'))
                        <span class="text-danger text-left">{{ $errors->first('ResolucionMinisterial') }}</span>
                    @endif
                </div>



                <div class="form-group form-floating mb-3">
                    <select class="form-control" name="college_id"  autofocus>
                        <option value="" disabled selected>Seleccione</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{$college->name}}</option>
                        @endforeach

                    </select>
                    <label for="floatingName">Departamento</label>
                    @if ($errors->has('college_id'))
                        <span class="text-danger text-left">{{ $errors->first('college_id') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <select class="form-control" name="years" value="{{ old('years') }}"  autofocus>
                        <option value="" disabled selected>Seleccione</option>
                        <option value="0.5" > 1 Semestre (Medio Año)</option>
                        <option value="1" > 2 Semestres (Un Año)</option>
                        <option value="1.5" > 3 Semestres (Un Año y medio)</option>
                        <option value="2" > 4 Semestres (Dos Años)</option>
                        <option value="2.5" > 5 Semestres (Dos Años y medio)</option>
                        <option value="3" > 6 Semestres (Tres Años)</option>
                        <option value="3.5" > 7 Semestres (Tres Años y medio)</option>
                        <option value="4" > 8 Semestres (Cuatro Años)</option>
                        <option value="4.5" > 9 Semestres (Cuatro Años y medio)</option>
                        <option value="5" > 10 Semestres (Cinco Años)</option>
                        <option value="5.5" > 11 Semestres (Cinco Años y medio)</option>
                        <option value="6" > 12 Semestres (Seis Años)</option>
                        <option value="6.5" > 13 Semestres (Seis Años y medio)</option>

                    </select>
                    <label for="floatingName">Años de duración (en semestres)</label>
                    @if ($errors->has('years'))
                        <span class="text-danger text-left">{{ $errors->first('years') }}</span>
                    @endif
                </div>
                <div class="form-group form-floating mb-3">
                    <div class="h6 pb-2 mb-4 text-success border-bottom border-success">
                        Coordinador/es <span class="text-small small"> (puede seleccionar más de uno)</span>
                    </div>

                    <select class="multi" name="coordinador_id[]" autofocus id="coordinador_id[]" multiple="multiple" size="5"
                            style="width: 100%">

                        @foreach($coords as $coord)
                            <option value="{{ $coord->user->id }}"
                            >{{$coord->name.' '.$coord->lastname}}</option>
                        @endforeach

                    </select>

                    @if ($errors->has('coordinador_id'))
                        <span class="text-danger text-left">{{ $errors->first('coordinador_id') }}</span>
                    @endif
                </div>



                <button class="w-100 btn btn-lg btn-primary" type="submit">Registrar</button>

                @include('auth.partials.copy')
            </form>
            <script>
                $(document).ready(function () {
                    $('.multi').select2();
                });

            </script>
        @endauth

        @guest
            <h1>¡Bienvenido!</h1>
            <div class="d-flex justify-content-center">
                <iframe width="923" height="519" src="https://www.youtube.com/embed/REnyeTuFR98" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen ></iframe>
            </div>

        @endguest
    </div>
@endsection

