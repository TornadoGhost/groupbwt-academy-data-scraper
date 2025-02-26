@extends('layouts.app')

@section('subtitle', 'Create products')
@section('content_header_title', 'Products')
@section('content_header_subtitle', 'Create')

@section('content_body')
    <form id="product-create">
        <div class="row">
            <div class="form-group col-md-4">
                <label for="title">Title</label>
                <div class="input-group">
                    <input class="form-control" id="title" name="title"
                           type="text" placeholder="Enter title name of product">
                    <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="manufacturer_part_number">Manufacturer part number</label>
                <div class="input-group">
                    <input class="form-control" id="manufacturer_part_number" name="manufacturer_part_number"
                           type="text" placeholder="Enter product manufacturer part number">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="pack_size">Pack size</label>
                <div class="input-group">
                    <input class="form-control" id="pack_size" name="pack_size"
                           type="text" placeholder="Enter product pack size">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Retailers</label>
                <div id="retailers"></div>
            </div>
        </div>
        <label>Images</label>
        <input id="input-b5" name="images[]" type="file" class="mb-2" multiple>
        <x-adminlte-button class="d-block btn-flat rounded mt-2" id="save-button" type="submit" label="Save" theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <div class="modal fade" id="modalWindow">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"></div>
                <div class="modal-body"></div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script type="module" src="{{ asset('js/products/create.js') }}"></script>
@endpush
