{{--TODO admin views, can be deleted or saved for future, because not used right now--}}
@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@stop

@section('subtitle', 'Users')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Users')
@section('content_header_subtitle_subtitle', 'All')
@section('plugins.bootstrap-bs', true)

{{-- Content body: main page content --}}

@section('content_body')
    @php
        $heads = [
            'id',
            'Username',
            'Email',
            'Region',
            'Created',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
            ];
    @endphp
    @php
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
    <x-adminlte-button class="d-none" id="modal-delete-btn" label="Delete Users" data-toggle="modal"
                       data-target="#modalMin"/>
    <x-adminlte-modal id="errors-modal" title="Error" theme="red">
        Error
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="error-modal-button" label="Error" data-toggle="modal"
                       data-target="#errors-modal"/>
@stop
{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";
        import {updatePrepareData} from "{{ asset('js/updatePrepareData.js') }}";

        async function getTableData() {
            let data;
            await mainFetch(`users`, 'GET')
                .then((response) => {
                    if (response?.data) {
                        data = response.data;
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
                        {"data": "username"},
                        {"data": "email"},
                        {"data": "region"},
                        {"data": "created_at"},
                        {
                            "data": null,
                            "render": function () {
                                return `
                            <button id="edit-row" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button id="delete-row" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                        `;
                            },
                            "orderable": false,
                            "searchable": false
                        },
                    ],
                });

                const removeButtons = document.querySelectorAll('button[id=delete-row]');
                removeButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        document.getElementById('modal-delete-btn').click();
                        const row = getRow(event);
                        modalRemoveAccept(row);
                        document.removeEventListener('click', modalRemoveAccept);
                    });
                });
                const editButtons = document.querySelectorAll('button[id=edit-row]');
                editButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const row = getRow(event)
                        const id = getRowId(row);
                        window.location.href = `users/${id}`;
                    });
                });

                function modalRemoveAccept(element) {
                    document.addEventListener('click', function (event) {
                        if (event.target === document.getElementById('delete-btn')) {
                            const id = getRowId(element);
                            mainFetch(`users/${id}`, 'delete')
                                .then(response => {
                                    console.log(response)
                                    if (response?.status === 'Error') {
                                        setErrorWindow('Error', response.message, 'red');
                                    } else {
                                        table.row(element).remove().draw()
                                    }
                                });
                        }
                    })
                }

                function getRow(element) {
                    return element.target.closest('tr');
                }

                function getRowId(element) {
                    return table.row(element).data().id;
                }
            }

            function setErrorWindow(title, body, theme) {
                const modalOpenButton = document.getElementById('error-modal-button');
                const errorModal = document.getElementById('errors-modal');
                const modalBody = errorModal.getElementsByClassName('modal-body')[0];
                const modalTitle = errorModal.getElementsByClassName('modal-title')[0];
                const modalHeader = modalTitle.parentElement;
                const modalCloseButton = errorModal.getElementsByClassName('modal-footer')[0].children[0];

                modalTitle.innerHTML = title;
                modalHeader.classList.remove('bg-red');
                modalHeader.classList.add(`bg-${theme}`);
                modalBody.innerHTML = body;
                modalCloseButton.classList.remove('bg-red');
                modalCloseButton.classList.add(`bg-${theme}`);

                modalOpenButton.click();
            }

            if ($.fn.DataTable.isDataTable('#table2')) {
                $('#table2').DataTable().clear().destroy();
            }
            initTable();
        });
    </script>
@endpush
