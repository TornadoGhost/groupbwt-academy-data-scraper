@extends('layouts.app')

@section('subtitle', 'Products')
@section('content_header_title', 'Products')

@section('content_body')
    <div class="btn-group mb-2">
        <a href="{{ route('products.create') }}">
            <x-adminlte-button class="mr-1" label="Create" theme="primary"/>
        </a>
        <div>
            <x-adminlte-button class="mr-1" id="import-modal-btn" label="Import" theme="primary" data-toggle="modal"
                               data-target="#import-modal"/>
        </div>
        <div>
            <x-adminlte-button id="export-btn" label="Export" theme="primary"/>
        </div>
    </div>
    @php
        $heads = [
            'id',
            'Title',
            'Manufacturer part number',
            'Pack size',
            'Created',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];
        $config['dom'] = '<"row" <"col-sm-7" B> <"col-sm-5 d-flex justify-content-end" i> >
                  <"row" <"col-12" tr> >
                  <"row" <"col-sm-12 d-flex justify-content-start" f> >';
        $config['paging'] = false;
        $config["lengthMenu"] = [ 10, 50, 100, 500];
    @endphp
    <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config"
                          striped bordered compressed beautify with-buttons hoverable/>
    <x-adminlte-modal id="modalMin" title="Warning" theme="red">
        <p>Are you sure, you want to delete?</p>
        <x-slot name="footerSlot">
            <x-adminlte-button id="close-btn" class="mr-auto" theme="danger" label="Close" data-dismiss="modal"/>
            <x-adminlte-button id="delete-btn" theme="success" label="Delete" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="import-modal" title="Import products" theme="blue">
        <a href="{{ route('products.exampleCsv') }}">
            <x-adminlte-button class="btn-sm mb-2" id="example-scv" label="Download example file"
                               title="Download example CSV file" theme="primary" type="button"/>
        </a>
        <div class="mr-1" id="import-block">
            <form id="import-form">
                <x-adminlte-input-file id="import-file" name="csv_file" accept=".csv"
                                       placeholder="Choose csv file..." igroup-size="md" legend="Choose">
                    <x-slot name="appendSlot">
                        <x-adminlte-button type="submit" id="import-btn" theme="primary" label="Import file"/>
                    </x-slot>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-primary">
                            <i class="fas fa-file-upload"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-file>
                <p class="d-none text-danger m-0" id="input-file-error"></p>
            </form>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button id="close-btn" class="mr-auto" theme="danger" label="Close" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="modal-delete-btn" label="Delete Product" data-toggle="modal"
                       data-target="#modalMin"/>
    <x-adminlte-modal id="error-modal" title="Error">
        Error
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="error-modal-button" label="Error" data-toggle="modal"
                       data-target="#error-modal"/>
@stop

@push('js')
    <script type="module" src="{{ asset('js/products/index.js') }}"></script>
@endpush
