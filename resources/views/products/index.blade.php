@extends('layouts.app')

@section('subtitle', 'Products')
@section('content_body')
    <div class="content-header pl-0 d-flex justify-content-between align-items-center">
        <h1 class="text-muted">
            Products
        </h1>
    </div>
    <div class="btn-group mb-2">
        <a href="{{ route('products.create') }}">
            <x-adminlte-button class="mr-1" label="Create" theme="primary"/>
        </a>
        <div>
            <x-adminlte-button id="export-btn" label="Export" theme="primary"/>
        </div>
        <div>
            <button class="btn btn-primary ml-1" id="importButton" type="button">
                Import
            </button>
        </div>
    </div>
    <x-table id="table2">
        <th>
            <div class="d-flex justify-content-between">
                <span>ID</span>
                <div>
                    <i class="bi bi-sort-up text-secondary" role="button" data-column="id" data-order="asc"></i>
                    <i class="bi bi-sort-down text-secondary" role="button" data-column="id"
                       data-order="desc"></i>
                </div>
            </div>
        </th>
        <th>
            <div class="d-flex justify-content-between">
                <span>Title</span>
                <div>
                    <i class="bi bi-sort-up text-secondary" role="button" data-column="title"
                       data-order="asc"></i>
                    <i class="bi bi-sort-down text-secondary" role="button" data-column="title"
                       data-order="desc"></i>
                </div>
            </div>
        </th>
        <th>
            <div class="d-flex justify-content-between">
                <span>Manufacturer part number</span>
                <div>
                    <i class="bi bi-sort-up text-secondary" role="button" data-column="manufacturer_part_number"
                       data-order="asc"></i>
                    <i class="bi bi-sort-down text-secondary" role="button"
                       data-column="manufacturer_part_number"
                       data-order="desc"></i>
                </div>
            </div>
        </th>
        <th>
            <div class="d-flex justify-content-between">
                <span>Pack size</span>
                <div>
                    <i class="bi bi-sort-up text-secondary" role="button" data-column="pack_size"
                       data-order="asc"></i>
                    <i class="bi bi-sort-down text-secondary" role="button" data-column="pack_size"
                       data-order="desc"></i>
                </div>
            </div>
        </th>
        <th>
            <div class="d-flex justify-content-between">
                <span>Created</span>
                <div>
                    <i class="bi bi-sort-up text-secondary" role="button" data-column="created_at"
                       data-order="asc"></i>
                    <i class="bi bi-sort-down text-secondary" role="button" data-column="created_at"
                       data-order="desc"></i>
                </div>
            </div>
        </th>
        <th>
            <div class="d-flex justify-content-between">
                <span>Actions</span>
            </div>
        </th>
    </x-table>
    {{--  TODO: change from x-adminlte to regular html+js  --}}
    {{--<x-adminlte-modal id="modalMin" title="Warning" theme="red">
        <p>Are you sure, you want to delete?</p>
        <x-slot name="footerSlot">
            <x-adminlte-button id="close-btn" class="mr-auto" theme="danger" label="Close" data-dismiss="modal"/>
            <x-adminlte-button id="delete-btn" theme="success" label="Delete" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="modal-delete-btn" label="Delete Product" data-toggle="modal"
                       data-target="#modalMin"/>
    <x-adminlte-modal id="error-modal" title="Error">
        Error
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="error-modal-button" label="Error" data-toggle="modal"
                       data-target="#error-modal"/>--}}

    <!-- Модальне вікно -->
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
    <script type="module">
        import {exportData} from "{{ asset('js/exportData.js') }}";
        import {productsTable} from "{{ asset('js/table/productsTable.js') }}";
        import {productImportModalWindow} from "{{ asset('js/modalWindows/productImportModalWindow.js') }}";


        //  TODO: previous and next buttons do not working
        productsTable();
        productImportModalWindow();

        // TODO: redo export
        function exportScrapedDataRetailer() {
            const exportBtn = document.getElementById('export-btn');
            exportBtn.addEventListener('click', function () {
                const successAlert = `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Started" dismissable>
                                        Export started! Wait for a notification.
                                    </x-adminlte-alert>`;
                exportData('export/products', successAlert);
            });
        }exportScrapedDataRetailer();
    </script>
@endpush
