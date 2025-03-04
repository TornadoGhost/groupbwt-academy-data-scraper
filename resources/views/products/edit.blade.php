@extends('layouts.app')

@section('subtitle', 'Edit product')
@section('content_header_title', 'Products')
@section('content_header_subtitle', 'Edit')
@section('plugins.inputFileKrajee', true)

@section('content_body')
    <form id="product-update">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="title">Title</label>
                <div class="input-group">
                    <input class="form-control" id="title" name="title"
                           type="text" value="{{ $product['title'] }}" placeholder="Enter title name of product">
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
                           type="text" value="{{ $product['manufacturer_part_number'] }}"
                           placeholder="Enter product manufacturer part number">
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
                           type="text" value="{{ $product['pack_size'] }}" placeholder="Enter product pack size">
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
        <input id="input-b5" name="images[]" type="file" class="mb-2" multiple>
        <x-adminlte-button class="d-block btn-flat rounded mt-2" id="save-button" type="button" label="Save"
                           theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>Product updated.</p>
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="modal-open-btn" label="Open Modal" data-toggle="modal"
                       data-target="#modalMin"/>
@stop

@push('js')
    <script type="module" src="{{ asset('js/products/edit.js') }}"></script>
@endpush
