@extends('layouts.app')

@section('subtitle', 'Create products')
@section('content_header_title', 'Products')
@section('content_header_subtitle', 'Create')

@section('content_body')
    <form id="product-create">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="title">Title</label>
                <div class="input-group">
                    <input class="form-control" id="title" name="title"
                           type="text" placeholder="Enter title name of product">
                    <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="manufacturer_part_number">Manufacturer part number</label>
                <div class="input-group">
                    <input class="form-control" id="manufacturer_part_number" name="manufacturer_part_number"
                           type="text" placeholder="Enter product manufacturer part number">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
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
            <div class="form-group col-md-6">
                <label>Retailers</label>
                <div class="input-group" id="retailers"></div>
            </div>
        </div>
        <label>Images</label>
        <input id="input-b5" name="images[]" type="file" class="mb-2" multiple>
        <x-adminlte-button class="d-block btn-flat rounded mt-2" id="save-button" type="submit" label="Save" theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>Product was created.</p>
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="modal-open-btn" label="Open Modal" data-toggle="modal"
                       data-target="#modalMin"/>
@stop

@push('js')
    <script type="module" src="{{ asset('js/products/create.js') }}"></script>
@endpush
