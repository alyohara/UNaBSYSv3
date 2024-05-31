@extends('layouts.app-master')



@section('content')
    @include('layouts.partials.messages')
    <div class="bg-light p-5 rounded">
        @auth
            @if(Auth::user()->userData->hasRole('admin') || Auth::user()->userData->hasRole('acaUno'))
                <form method="post" action="{{ route('cargo.simplificadaCargaAdmin') }}">
                    <a style="margin-bottom: 5px; float: right" href="#" class="  btn btn-secondary "
                       onclick="window.history.back();">Volver</a>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                    <h1 class="h3 mb-3 fw-normal">Nuevos Cargos</h1>
                    <h4 class="h5 mb-3 fw-normal">Carga Simplificada</h4>


                    <table class="table">
                        <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Comisión</th>
                            <th>Profesor</th>
                            <th>Categoría</th>
                            <th>Dedicación</th>
                            <th>Condición</th>
                            <th>Observaciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cargos as $cargo)
                            <tr data-cargo-id="{{ $cargo->id }}">
                                <td>{{ $cargo->materia->code.' - '.$cargo->materia->name }}</td>
                                <td class="lalala">
                                    <select class="form-control comision" name="comision[{{ $cargo->id }}]">
                                        @for($i = 1; $i <= 20; $i++)
                                            <option value="{{ $i }}"
                                                    @if ($cargo->comision == $i) selected @endif>{{ $i }}</option>

                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control profesor" id="profesor"
                                            name="profesor[{{ $cargo->id  }}]"
                                            required>
                                        @foreach($profesors as $profesor)
                                            <option value="{{ $profesor->id }}" {{ $cargo->persona_id == $profesor->id ? 'selected' : '' }}>{{ $profesor->lastname.', '.$profesor->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $cargo->categoria}}</td>
                                <td>{{ $cargo->dedicacion_horaria}}</td>
                                <td>{{ $cargo->tipo}}</td>
                                <td>
                                    <textarea class="form-control observaciones" id="observaciones"
                                              name="observaciones[{{ $cargo->id  }}]"
                                              rows="1" required>{{ $cargo->observaciones }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


                    {{--                    <button type="submit" class="btn btn-primary">Actualizar</button>--}}

                </form>


                <script>
                    $(document).ready(function () {
                        $('.comision').change(function () {
                            let comision = $(this).val();
                            let profesor = $(this).closest('tr').find('.profesor').val();
                            let cargoId = $(this).closest('tr').data('cargo-id');
                            let observaciones = $(this).closest('tr').find('.observaciones').val();

                            $.ajax({
                                url: '{{ route('cargo.simplificadaCargaCoord') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    observaciones: observaciones,
                                    comision: comision,
                                    profesor: profesor,
                                    cargoId: cargoId
                                },
                                success: function (response) {
                                    toastr.success(response.message);
                                }
                            });
                        });

                        $('.profesor').change(function () {
                            let comision = $(this).val();
                            let profesor = $(this).closest('tr').find('.profesor').val();
                            let cargoId = $(this).closest('tr').data('cargo-id');
                            let observaciones = $(this).closest('tr').find('.observaciones').val();

                            $.ajax({
                                url: '{{ route('cargo.simplificadaCargaCoord') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    observaciones: observaciones,
                                    comision: comision,
                                    profesor: profesor,
                                    cargoId: cargoId
                                },
                                success: function (response) {
                                    toastr.success(response.message);
                                }
                            });
                        });

                        $('.observaciones').change(function () {
                            let comision = $(this).val();
                            let profesor = $(this).closest('tr').find('.profesor').val();
                            let cargoId = $(this).closest('tr').data('cargo-id');
                            let observaciones = $(this).closest('tr').find('.observaciones').val();

                            $.ajax({
                                url: '{{ route('cargo.simplificadaCargaCoord') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    observaciones: observaciones,
                                    comision: comision,
                                    profesor: profesor,
                                    cargoId: cargoId
                                },
                                success: function (response) {
                                    toastr.success(response.message);
                                }
                            });
                        });
                    });


                </script>
            @endif
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


