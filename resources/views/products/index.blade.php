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
{{--    <script type="module" src="{{ asset('js/products/index.js') }}"></script>--}}
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";
        import {showAlert} from "{{ asset('js/showAlert.js') }}";
        import {exportData} from "{{ asset('js/exportData.js') }}";

        async function getTableData() {
            let data;
            await mainFetch(`products`, 'GET')
                .then((response) => {
                    if (response.data) {
                        data = response.data;
                    } else {
                        console.log(response)
                    }
                });
            return data;
        }

        $(document).ready(function () {
            async function initTable() {
                const table = new DataTable('#table2', {
                    "data": await getTableData(),
                    "layout": {
                        topStart: 'buttons'
                    },
                    "order": [[0, 'desc']],
                    "columns": [
                        {"data": "id"},
                        {"data": "title"},
                        {"data": "manufacturer_part_number"},
                        {"data": "pack_size"},
                        {"data": "created_at"},
                        {
                            "data": null,
                            "render": function () {
                                return `
                            <button id="product-show" class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                               <i class="fa fa-lg fa-fw fa-eye"></i>
                            </button>
                            <button id="product-edit" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button id="product-delete" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                        `;
                            },
                            "orderable": false,
                            "searchable": false
                        }
                    ],
                });

                const showButtons = document.querySelectorAll('button[id=product-show]');
                showButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const id = getRowData(event.target.closest('tr')).id;
                        window.location.href = `products/${id}`;
                    });
                })

                const removeButtons = document.querySelectorAll('button[id=product-delete]');
                removeButtons.forEach(elem => {
                    const handler = function (event) {
                        document.getElementById('modal-delete-btn').click();
                        modalRemoveProductAccept(event.target.closest('tr'));
                        elem.removeEventListener('click', handler);
                    };
                    elem.addEventListener('click', handler);
                });

                const editButtons = document.querySelectorAll('button[id=product-edit]');
                editButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const id = getRowData(event.target.closest('tr')).id;
                        window.location.href = `products/${id}/edit`;
                    });
                })

                function modalRemoveProductAccept(element) {
                    const handler = function (event) {
                        if (event.target === document.getElementById('delete-btn')) {
                            const id = getIdFromRow(element);
                            mainFetch(`products/${id}`, 'delete')
                                .then(response => {
                                    if (response?.status === 'Error') {
                                        setModalWindow('Error', response.message);
                                    } else {
                                        table.row(element).remove().draw();
                                    }
                                    document.removeEventListener('click', handler);
                                });
                        }
                    };
                    document.addEventListener('click', handler);
                }

                function getIdFromRow(element) {
                    return getRowData(element).id;
                }

                function getRowData(element) {
                    return table.row(element).data();
                }
            }

            if ($.fn.DataTable.isDataTable('#table2')) {
                $('#table2').DataTable().clear().destroy();
            }

            initTable();

            function importData(formId) {
                const btn = document.getElementById(formId);
                btn.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const successAlert =
                        `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Done" dismissable>
                                        Import started! Waiting please.
                                    </x-adminlte-alert>`;
                    showAlert(successAlert, 'content-wrapper', true, 5000);

                    const formData = new FormData(e.target);
                    mainFetch('import/products', 'POST', formData)
                        .then(response => {
                            if (response.status === 'Success') {
                                removeInputError();
                                const input = e.target.querySelector('#import-file');
                                input.value = '';
                                input.nextElementSibling.innerHTML = 'Choose csv import file...';
                            } else if (response.status === 'Error' && response.data) {
                                removeInputError();

                                const block = document.createElement('div');
                                response.data.forEach(error => {
                                    const errorMessage = document.createElement('p');
                                    errorMessage.textContent = `${error}`;
                                    block.appendChild(errorMessage);
                                });
                                const title = 'CSV validation errors';

                                setModalWindow(title, block);
                            } else if (response.errors) {
                                const inputErrorMessage = document.getElementById('input-file-error');
                                inputErrorMessage.previousElementSibling.classList.add('mb-0');
                                inputErrorMessage.classList.remove('d-none');
                                inputErrorMessage.innerHTML = response.errors['csv_file'];
                            }
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
                });
            }

            importData('import-form');
        });

        function removeInputError() {
            const inputErrorMessage = document.getElementById('input-file-error');
            if (!inputErrorMessage.classList.contains('d-none')) {
                inputErrorMessage.classList.add('d-none');
            }
        }

        function getProductId(button) {
            return button.closest('tr[class=odd]').firstElementChild.textContent;
        }

        function setModalWindow(title, body, theme = 'red') {
            const modal = document.getElementById('error-modal');
            const modalHeader = modal.getElementsByClassName('modal-header')[0];
            const modalTitle = modal.getElementsByClassName('modal-title')[0];
            const modalBody = modal.getElementsByClassName('modal-body')[0];
            const modalFooter = modal.getElementsByClassName('modal-footer')[0];

            modalTitle.innerHTML = title;
            modalBody.innerHTML = '';
            modalBody.appendChild(body);

            modalHeader.classList.add(`bg-${theme}`);
            modalFooter.querySelector('button.btn.btn-default').classList.add(`bg-red`);

            document.getElementById('error-modal-button').click();
        }

        function exportScrapedDataRetailer() {
            const exportBtn = document.getElementById('export-btn');
            exportBtn.addEventListener('click', function() {
                const successAlert = `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Started" dismissable>
                                        Export started! Wait for a notification.
                                    </x-adminlte-alert>`;
                exportData('export/products', successAlert);
            });
        }exportScrapedDataRetailer();

    </script>
@endpush
