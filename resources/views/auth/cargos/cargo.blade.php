@extends('layouts.app-master')



@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light p-5 rounded">
        @auth
            <form method="post" action="{{ route('cargo.update') }}">
                <a style="margin-bottom: 5px; float: right" href="#" class="  btn btn-secondary "
                   onclick="window.history.back();">Volver</a>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>


                <h1 class="h3 mb-3 fw-normal">Modificación de Cargo</h1>


                <div class="h5 pb-2 mb-4 text-success border-bottom border-success">
                    Datos del Cargo
                    <input type="hidden" value="{{$cargo->id}}" name="cargo_id" id="cargo_id">
                </div>
                <div class="row">
                    <div class="form-group form-floating mb-3 col">
                        <select class="form-control" name="tipo" autofocus required>
                            <option value="" disabled>Seleccione</option>
                            <option value="Ordinario/Concursado"
                                    @if ($cargo->tipo == 'Ordinario/Concursado') selected @endif >Ordinario/Concursado
                            </option>
                            <option value="Interino" @if ($cargo->tipo == 'Interino') selected @endif>Interino</option>
                            <option value="Contratado" @if ($cargo->tipo == 'Contratado') selected @endif>Contratado
                            </option>
                        </select>
                        <label for="floatingName">Tipo</label>
                        @if ($errors->has('cargo'))
                            <span class="text-danger text-left">{{ $errors->first('cargo') }}</span>
                        @endif
                    </div>
                    <div class="form-group form-floating mb-3 col">
                        <input type="date" class="form-control" id="floatingName" name="fecha_alta"
                               value="{{ $cargo->fecha_alta }}" required>
                        <label for="floatingName">Fecha de Alta</label>
                        @if ($errors->has('fecha_alta'))
                            <span class="text-danger text-left">{{ $errors->first('fecha_alta') }}</span>
                        @endif
                    </div>
                    <div class="form-group form-floating mb-3 col">
                        <input type="date" class="form-control" id="floatingName" name="fecha_baja"
                               value="{{ $cargo->fecha_baja }}">
                        <label for="floatingName">Fecha de Baja</label>
                        @if ($errors->has('fecha_baja'))
                            <span class="text-danger text-left">{{ $errors->first('fecha_baja') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group form-floating mb-3 col" style="margin-bottom: 25px">
                        <input type="text" class="form-control" id="floatingName" name="act_des"
                               value="{{$cargo->act_des }}" required>
                        <label for="floatingName">Acto Administrativo / Designación</label>
                        @if ($errors->has('act_des'))
                            <span class="text-danger text-left">{{ $errors->first('act_des') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group form-floating mb-3 col">
                        <select class="form-control" name="categoria" autofocus >
                            <option value="" disabled selected>Seleccione</option>

                                <option value="Profesor Titular (TIT)"
                                        @if ($cargo->categoria == 'Profesor Titular (TIT)') selected @endif>Profesor
                                    Titular (TIT)
                                </option>
                                <option value="Profesor Asociado (ASO)"
                                        @if ($cargo->categoria == 'Profesor Asociado (ASO)') selected @endif>Profesor
                                    Asociado (ASO)
                                </option>
                                <option value="Profesor Adjunto (ADJ)"
                                        @if ($cargo->categoria == 'Profesor Adjunto (ADJ)') selected @endif>Profesor
                                    Adjunto (ADJ)
                                </option>
                                <option value="Auxiliar Jefe de Trabajos Prácticos (JTP)"
                                        @if ($cargo->categoria == 'Auxiliar Jefe de Trabajos Prácticos (JTP)') selected @endif>Auxiliar Jefe de Trabajos
                                    Prácticos (JTP)
                                </option>
                                <option value="Auxiliar Ayudante (AuxA) "
                                        @if ($cargo->categoria == 'Auxiliar Ayudante (AuxA)') selected @endif>Auxiliar
                                    Ayudante (AuxA)
                                </option>
                                <option value="Auxiliar Ayudante Alumno (AuxAA)"
                                        @if ($cargo->categoria == 'Auxiliar Ayudante Alumno (AuxAA)') selected @endif>
                                    Auxiliar Ayudante Alumno (AuxAA)
                                </option>



                        </select>
                        <label for="floatingName">Categoria</label>
                        @if ($errors->has('categoria'))
                            <span class="text-danger text-left">{{ $errors->first('categoria') }}</span>
                        @endif
                    </div>
                    <div class="form-group form-floating mb-3 col">
                        <select class="form-control" name="dedicacion_horaria" autofocus >
                            <option value="" disabled>Seleccione</option>
                            <option value="Dedicación Exclusiva"
                                    @if($cargo->dedicacion_horaria == 'Dedicación Exclusiva') selected @endif>Dedicación
                                Exclusiva
                            </option>
                            <option value="Dedicación Semi Exclusiva"
                                    @if($cargo->dedicacion_horaria == 'Dedicación Semi Exclusiva') selected @endif>
                                Dedicación Semi Exclusiva
                            </option>
                            <option value="Dedicación Simple"
                                    @if($cargo->dedicacion_horaria == 'Dedicación Simple') selected @endif>Dedicación
                                Simple
                            </option>
                            <option value="Ad Honorem" @if($cargo->dedicacion_horaria == 'Ad Honorem') selected @endif>
                                Ad
                                Honorem
                            </option>
                        </select>
                        <label for="floatingName">Dedicación Horaria</label>
                        @if ($errors->has('dedicacion_horaria'))
                            <span class="text-danger text-left">{{ $errors->first('dedicacion_horaria') }}</span>
                        @endif
                    </div>
                </div>

                    <div class="h5 pb-2 mb-4 text-success border-bottom border-success">
                        Materia
                    </div>

                <div class="row">
{{--                    <div class="form-group form-floating mb-3 col">--}}
{{--                        <select class="form-control" name="college_id" id="college_id" autofocus>--}}
{{--                            <option value="" disabled>Seleccione</option>--}}
{{--                            @foreach($colleges as $col)--}}
{{--                                <option value="{{ $col->id }}"--}}
{{--                                        @if($college == $col->id) selected @endif>{{$col->name}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                        <label for="floatingName">Departamento</label>--}}
{{--                        @if ($errors->has('college_id'))--}}
{{--                            <span class="text-danger text-left">{{ $errors->first('college_id') }}</span>--}}
{{--                        @endif--}}
{{--                    </div>--}}

{{--                    <div class="form-group form-floating mb-3 col">--}}
{{--                        <select class="form-control" name="career_id" autofocus id="career_id">--}}
{{--                            <option value="" disabled>Seleccione</option>--}}
{{--                            @foreach($careers as $car)--}}
{{--                                <option value="{{ $car->id }}"--}}
{{--                                        @if($career == $car->id) selected @endif>{{$car->name}}</option>--}}
{{--                            @endforeach--}}


{{--                        </select>--}}
{{--                        <label for="floatingName">Carrera</label>--}}
{{--                        @if ($errors->has('career_id'))--}}
{{--                            <span class="text-danger text-left">{{ $errors->first('career_id') }}</span>--}}
{{--                        @endif--}}
{{--                    </div>--}}

                    <div class="form-group form-floating mb-3 col">
                        <select class="form-control" name="subject_id" autofocus id="subject_id" required>
                            <option value="" disabled>Seleccione</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" >{{$subject->code.' - '.$subject->name}}</option>
                            @endforeach

                        </select>

                        @if ($errors->has('subject_id'))
                            <span class="text-danger text-left">{{ $errors->first('subject_id') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group form-floating mb-3 col">
                        <select class="form-control" name="comision" autofocus required>
                            <option value="" disabled selected>Seleccione</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}" @if($cargo->comision == $i) selected @endif>{{$i}}</option>
                            @endfor
                        </select>
                        <label for="floatingName">Comisión</label>
                        @if ($errors->has('comision'))
                            <span class="text-danger text-left">{{ $errors->first('comision') }}</span>
                        @endif
                    </div>
                </div>



                    <div class="h5 pb-2 mb-4 text-success border-bottom border-success">
                        Postulante
                    </div>

                <div class="row">
                    <div class="form-group form-floating mb-3 col">
                        <select class="form-control" name="persona_id" id="persona_id" autofocus required>
                            <option value="" disabled selected>Seleccione</option>
                            @foreach($profesors as $profesor)
                                <option value="{{ $profesor->id }}"
                                        @if($cargo->persona_id == $profesor->id) selected @endif>{{$profesor->name}}, {{$profesor->lastname}}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('persona_id'))
                            <span class="text-danger text-left">{{ $errors->first('persona_id') }}</span>
                        @endif
                    </div>
                </div>


                <button class="w-100 btn btn-lg btn-primary" type="submit">Actualizar</button>

                @include('auth.partials.copy')
            </form>
            <script>

                $(function () {
                    $('#college_id').change(function () {

                        let type = "GET";
                        let college = $(this).val();
                        $('#career_id').empty().append('');
                        $('#subject_id').empty().append('');

                        let url = '{{ route('getCarreras', ":id") }}';
                        url = url.replace(':id', college);
                        let ajaxurl = url;
                        $.ajax({
                            type: type,
                            url: ajaxurl,
                            success: function (data) {
                                let carreras = data['careers'];
                                let options = '';
                                options += '<option value="" disabled selected>Seleccione</option>';
                                $('#subject_id').append(options);
                                for (let i = 0; i < carreras.length; i++) { // Loop through the data & construct the options
                                    options += '<option value="' + carreras[i].id + '">' + carreras[i].name + '</option>';
                                }
                                $('#career_id').append(options);
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });


                    });
                });
                $(function () {
                    $('#career_id').change(function () {

                        let type = "GET";
                        let career = $(this).val();
                        $('#subject_id').empty().append('');

                        let url = '{{ route('getMaterias', ":id") }}';
                        url = url.replace(':id', career);
                        let ajaxurl = url;
                        $.ajax({
                            type: type,
                            url: ajaxurl,
                            success: function (data) {
                                let materias = data['materias'];
                                let options = '';
                                options += '<option value="" disabled selected>Seleccione</option>';
                                for (let i = 0; i < materias.length; i++) { // Loop through the data & construct the options
                                    options += '<option value="' + materias[i].id + '">' + materias[i].name + '</option>';
                                }
                                $('#subject_id').append(options);
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });


                    });
                });
                $(document).ready(function() {
                    $('#subject_id').select2({
                        theme: "bootstrap"
                    });
                    $('#persona_id').select2({
                        theme: "bootstrap"
                    });
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

