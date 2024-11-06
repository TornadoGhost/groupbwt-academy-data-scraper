@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Exported Files')
@section('content_header_title', 'Exported files')

{{-- Content body: main page content --}}

@section('content_body')
    @php
        $heads = [
            'id',
            'File Name',
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
    <x-adminlte-button class="d-none" id="modal-delete-btn" label="Delete export" data-toggle="modal"
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

        async function getTableData() {
            let data;
            await mainFetch(`export-tables`, 'GET')
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
                        {"data": "file_name"},
                        {"data": "created_at"},
                        {
                            "data": null,
                            "render": function () {
                                return `
                            <button id="export-download" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Download">
                                <i class="fa fa-lg fa-fw fa-download text-success"></i>
                            </button>
                            <button id="export-delete" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                        `;
                            },
                            "orderable": false,
                            "searchable": false
                        }
                    ],
                });

                const downloadButtons = document.querySelectorAll('button[id=export-download]');
                downloadButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const header = {'Content-Type': 'application/json'};
                        const filePath = getRowData(event).path;
                        mainFetch(
                            'export-tables/download',
                            'POST',
                            JSON.stringify({'file_path': filePath}),
                            header,
                            null,
                            false
                        )
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = url;
                                a.download = `${getRowData(event).file_name}.xlsx`;
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                            })
                            .catch(error => {
                                console.error('Помилка при скачуванні файлу:', error);
                            });
                    });
                });

                const removeButtons = document.querySelectorAll('button[id=export-delete]');
                removeButtons.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        document.getElementById('modal-delete-btn').click();
                        modalRemoveExportAccept(event.target.closest('tr'));
                    });
                });

                function modalRemoveExportAccept(element) {
                    const handle = function (event) {
                        if (event.target === document.getElementById('delete-btn')) {
                            const id = table.row(element).data().id;
                            mainFetch(`export-tables/${id}`, 'delete')
                                .then(response => {
                                    if (response?.status === 'Error') {
                                        setErrorModalWindow(response.message);
                                    } else {
                                        table.row(element).remove().draw();
                                    }
                                })
                            document.removeEventListener('click', handle)
                        }
                    };
                    document.addEventListener('click', handle)
                }

                function getRowData(event) {
                    return table.row(event.target.closest('tr')).data();
                }
            }

            if ($.fn.DataTable.isDataTable('#table2')) {
                $('#table2').DataTable().clear().destroy();
            }

            initTable();
        });

        function removeInputError() {
            const inputErrorMessage = document.getElementById('input-file-error');
            if (!inputErrorMessage.classList.contains('d-none')) {
                inputErrorMessage.classList.add('d-none');
            }
        }

        function getExportId(button) {
            return button.closest('tr[class=odd]').firstElementChild.textContent;
        }


        function setErrorModalWindow(body) {
            const errorModal = document.getElementById('errors-modal');
            const modalBody = errorModal.getElementsByClassName('modal-body')[0];
            modalBody.innerHTML = '';
            modalBody.appendChild(body);
            document.getElementById('error-modal-button').click();
        }
    </script>
@endpush
