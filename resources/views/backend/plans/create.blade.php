@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">Agregar Nuevo Plan</h5>
    </div>
    <form class="form form-horizontal mar-top" id="product_form" action="{{ route('plans.store') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="row gutters-5">
            <div class="col-lg">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Información del Plan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                Referencia<span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input class="form-control" name="referencia" type="text"
                                    placeholder="Referencia" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input class="form-control" name="nombre" type="text"
                                    placeholder="Nombre" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                Precio <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input class="form-control" name="precio" type="number" value="1" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <textarea name="descripcion" class="form-control" id="" cols="30" rows="10" required></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <button class="btn btn-primary" id="upload-product"
                                type="submit">Cargar Plan</button>
                        </div>
        
                    </div>
                </div>
                
            </div>
            
        </div>
       
    </form>
@endsection

@section('script')
    <script type="text/javascript">
        // $(document).ready(function(){
        //     $('body').addClass('side-menu-closed');
        // });
        $('#product_form').bind('submit', function(e) {
            // Disable the submit button while evaluating if the form should be submitted
            $("#upload-product").prop('disabled', true);

            var valid = true;

            if (!valid) {
                e.preventDefault();

                // Reactivate the button if the form was not submitted
                $("#upload-product").button.prop('disabled', false);
            }
        });
    </script>
@endsection
