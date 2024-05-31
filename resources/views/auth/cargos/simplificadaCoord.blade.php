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

                    @foreach($cargos as $cargo)
                       

                        <div class="row form-row" style="font-size: smaller">
                            <div class="form-group col">
                                <label for="materia">Materia</label>
                                <select class="form-control" id="materia" name="materia" required>
                                    <option value="" selected>Seleccione</option>
                                    @foreach($materias as $materia)
                                        <option value="{{ $materia->id }}">{{ $materia->code.' - '.$materia->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="comision">Comisión</label>

                                <select class="form-control" id="comision" name="comision">
                                    <option value="" selected>Seleccione</option>
                                    @for($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="profesor">Profesor</label>
                                <select class="form-control" id="profesor" name="profesor" required>
                                    <option value="" selected>Seleccione</option>
                                    @foreach($profesors as $profesor)
                                        <option value="{{ $profesor->id }}">{{ $profesor->lastname.', '.$profesor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="categoria">Categoría</label>
                                <select class="form-control" name="categoria" id="categoria" autofocus>
                                    <option value="" disabled selected>Seleccione</option>

                                    <option value="Profesor Titular (TIT)">Profesor Titular (TIT)</option>
                                    <option value="Profesor Asociado (ASO)">Profesor Asociado (ASO)</option>
                                    <option value="Profesor Adjunto (ADJ)">Profesor Adjunto (ADJ)</option>
                                    <option value="Auxiliar Jefe de Trabajos Prácticos (JTP)">Auxiliar Jefe de Trabajos
                                        Prácticos (JTP)
                                    </option>
                                    <option value="Auxiliar Ayudante (AuxA)">Auxiliar Ayudante (AuxA)</option>
                                    <option value="Auxiliar Ayudante Alumno (AuxAA)">Auxiliar Ayudante Alumno (AuxAA)
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="dedicacion_horaria">Dedicación</label>
                                <select class="form-control" name="dedicacion_horaria" id="dedicacion_horaria"
                                        autofocus>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="Dedicación Exclusiva">Dedicación Exclusiva</option>
                                    <option value="Dedicación Semi Exclusiva">Dedicación Semi Exclusiva</option>
                                    <option value="Dedicación Simple">Dedicación Simple</option>
                                    <option value="Ad Honorem">Ad Honorem</option>
                                </select></div>

                            <div class="form-group col">
                                <label for="tipo">Condición</label>
                                <select class="form-control" name="tipo" id="tipo" autofocus required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="Ordinario/Concursado">Ordinario/Concursado</option>
                                    <option value="Interino">Interino</option>
                                    <option value="Contratado">Contratado</option>
                                </select></div>

                            <div class="form-group col">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>

                        </div>

                        <button type="button" class="btn btn-primary" id="addAnother">+ Agregar Otra</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    @endforeach

                </form>


                <script>
                    var rowIndex = 1;

                    document.getElementById('addAnother').addEventListener('click', function () {
                        // Get the form-row div
                        var formRow = document.querySelector('.form-row');

                        // Destroy Select2 on the select elements
                        $(formRow).find('select').each(function () {
                            $(this).select2('destroy');
                        });

                        // Clone the form-row div
                        var clone = formRow.cloneNode(true);

                        // Add a margin-top of 20px and a top border to the cloned div
                        clone.style.marginTop = '40px';
                        clone.style.borderTop = '1px solid #ececec'; // Change the color as needed

                        // Clear the input fields in the cloned div
                        var inputs = clone.querySelectorAll('input, select, textarea');
                        for (var i = 0; i < inputs.length; i++) {
                            inputs[i].value = '';
                        }

                        // Add the delete button to the cloned div
                        var deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.className = 'btn btn-outline-danger deleteRow btn-sm';
                        deleteButton.textContent = 'borrar';
                        deleteButton.style.marginTop = '5px';
                        deleteButton.style.position = 'absolute'; // Position the button absolutely
                        deleteButton.style.top = '0'; // Position the button at the top of the row
                        deleteButton.style.right = '0'; // Position the button at the right of the row
                        deleteButton.style.borderRadius = '10%'; // Make the button round
                        //deleteButton.style.width = '30px'; // Set a fixed width
                        deleteButton.style.height = '30px'; // Set a fixed height
                        deleteButton.addEventListener('click', function () {
                            // Get the form-row div
                            var formRow = this.parentNode.parentNode;

                            // Remove the form-row div
                            formRow.parentNode.removeChild(formRow);
                        });

                        var deleteButtonDiv = document.createElement('div');
                        deleteButtonDiv.className = 'form-group ';
                        deleteButtonDiv.style.position = 'relative'; // Position the parent div relatively
                        deleteButtonDiv.appendChild(deleteButton);

                        clone.appendChild(deleteButtonDiv);
                        // Append the cloned div to the form
                        formRow.parentNode.insertBefore(clone, formRow.nextSibling);


                        // Get the 'Add Another' button
                        var addAnotherButton = document.getElementById('addAnother');

                        // Insert the cloned div before the 'Add Another' button
                        addAnotherButton.parentNode.insertBefore(clone, addAnotherButton);

                        // Reinitialize Select2 for the new select elements
                        $(clone).find('select').each(function () {
                            $(this).select2({
                                theme: "bootstrap"
                            });
                        });

                        var inputs = clone.querySelectorAll('input, select, textarea');
                        for (var i = 0; i < inputs.length; i++) {
                            var name = inputs[i].name;
                            inputs[i].name = name + '[' + rowIndex + ']';
                        }

                        rowIndex++; // Increment the row index


                    });
                    document.querySelector('form').addEventListener('submit', function () {
                        // Get all the form-row divs
                        var formRows = document.querySelectorAll('.form-row');

                        // Loop through each form-row div
                        formRows.forEach(function (formRow, index) {
                            // Update the name attribute of each input field in the form-row div
                            var inputs = formRow.querySelectorAll('input, select, textarea');
                            for (var i = 0; i < inputs.length; i++) {
                                var name = inputs[i].name;
                                inputs[i].name = name + '[' + index + ']';
                            }
                        });
                    });
                    document.querySelectorAll('.deleteRow').forEach(function (button) {
                        button.addEventListener('click', function () {
                            // Get the form-row div
                            var formRow = this.parentNode.parentNode;

                            // Remove the form-row div
                            formRow.parentNode.removeChild(formRow);
                            rowIndex--; // Decrement the row index
                        });
                    });


                    $(document).ready(function () {
                        $('#materia').select2({
                            theme: "bootstrap",
                            dropdownAutoWidth: true
                        });
                        $('#profesor').select2({
                            theme: "bootstrap",
                            dropdownAutoWidth: true
                        });
                        $('#comision').select2({
                            theme: "bootstrap",
                            dropdownAutoWidth: true
                        });
                        $('#categoria').select2({
                            theme: "bootstrap",
                            dropdownAutoWidth: true
                        });
                        $('#dedicacion_horaria').select2({
                            theme: "bootstrap",
                            dropdownAutoWidth: true
                        });
                        $('#tipo').select2({
                            theme: "bootstrap",
                            dropdownAutoWidth: true
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


