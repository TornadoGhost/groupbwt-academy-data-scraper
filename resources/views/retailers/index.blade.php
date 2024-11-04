@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@stop

@section('subtitle', 'Retailers list')
@section('content_header_title', 'Retailers')
@section('plugins.bootstrap-bs', true)
@section('plugins.daterangepicker', true)

{{-- Content body: main page content --}}

@section('content_body')
    @if(Auth::user()->isAdmin)
        <a href="{{ route('retailers.create') }}">
            <x-adminlte-button class="mb-2" label="Create retailers" theme="primary"/>
        </a>
        @php
            $heads = [
                'Id',
                'Name',
                'Reference',
                'Currency',
                'Logo',
                'Created',
                'Active',
                ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];
        @endphp
    @else
        @php
            $heads = [
            'Id',
            'Name',
            'Reference',
            'Currency',
            'Logo',
            'Created At',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
            ];
        @endphp
    @endif
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
    <x-adminlte-button class="d-none" id="modal-delete-btn" label="Delete Retailer" data-toggle="modal"
                       data-target="#modalMin"/>
    <x-adminlte-modal id="errors-modal" title="Error" theme="red">
        Error
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="error-modal-button" label="Error" data-toggle="modal"
                       data-target="#errors-modal"/>
    <x-adminlte-modal id="export-modal" title="Export scraped data" theme="green">
        <p>Select the day for which you want to retrieve the scraped data</p>
        <x-adminlte-date-range id="scraped-date" name="date" label="Date" igroup-size="sm">
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </x-slot>
        </x-adminlte-date-range>
        <x-slot name="footerSlot">
            <x-adminlte-button id="export-close-btn" class="mr-auto" theme="danger" label="Close" data-dismiss="modal"/>
            <x-adminlte-button id="export-btn" theme="success" label="Export" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="retailer-access" title="Grand Access" size="md" theme="teal"
                      icon="fas fa-bolt" v-centered static-backdrop>
        <form id="grand-access-users">
            <input id="retailerId" type="hidden" name="retailer_id">
            <div>
                @php
                    $config = [
                        "title" => "Select multiple users...",
                        "liveSearch" => true,
                        "liveSearchPlaceholder" => "Search...",
                        "showTick" => true,
                        "actionsBox" => true,
                    ];
                @endphp
                <x-adminlte-select-bs id="optionsUser" name="users_id[]" label="Users"
                                      label-class="text-black" :config="$config" multiple>
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-teal">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                    <x-adminlte-options
                        :options="$preparedUsers"/>
                </x-adminlte-select-bs>
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button id="grand-access-save" theme="success" label="Save" data-dismiss="modal"/>
            </x-slot>
        </form>
    </x-adminlte-modal>
    <x-adminlte-button id="grand-access-button" data-toggle="modal" data-target="#retailer-access"
                       class="d-none bg-teal"/>
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
            await mainFetch(`retailers`, 'GET')
                .then((response) => {
                    if (response?.data) {
                        data = response.data;
                    }
                });
            return data;
        }

        $(document).ready(function () {
            const today = moment('{{ $firstDate }}');
            const lastDate = moment('{{ $lastDate }}')
            $('#scraped-date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                cancelButtonClasses: "btn-danger",
                locale: {format: "YYYY-MM-DD"},
                startDate: lastDate,
                endDate: lastDate,
                minDate: today,
                maxDate: lastDate,
            });

            async function initTable() {
                const table = new DataTable('#table2', {
                    "data": await getTableData(),
                    "layout": {
                        topStart: 'buttons'
                    },
                    "order": [[0, 'desc']],
                    "columns": [
                        {"data": "id"},
                        {"data": "name"},
                        {
                            "data": "reference",
                            "render": function (data, type, row) {
                                return `<a href="${data}">${data}</a>`;
                            }
                        },
                        {"data": "currency"},
                        {
                            "data": "logo_path",
                            "render": function (data, type, row) {
                                return `<img src="${data}" alt="image" width="65" height="50"/>`
                            },
                            "searchable": false,
                            "orderable": false,
                        },
                        {"data": "created_at"},
                            @if(Auth::user()->isAdmin)

                        {
                            "data": "isActive",
                            "render": function (data, type, row) {
                                return !!data;
                            }
                        },
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
                            <button id="restore-row" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Restore">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button id="access-row" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Access">
                                <i class="fa fa-lg fa-fw fa-universal-access"></i>
                            </button>
                        `;
                            },
                            "orderable": false,
                            "searchable": false
                        },
                        @else
                        {
                            "data": null,
                            "render": function () {
                                return `
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" id="export" data-toggle="modal"
                                data-target="#export-modal" title="Export scraped data">
                                <i class="fa fa-fw fa-table text-success"></i>
                            </button>`;
                            },
                            "orderable": false,
                            "searchable": false
                        },
                        @endif
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
                        window.location.href = `retailers/${id}`;
                    });
                });
                const restoreButton = document.querySelectorAll('button[id=restore-row]');
                restoreButton.forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        const row = getRow(event);
                        const retailerId = getRowId(row);
                        mainFetch(`retailers/${retailerId}/restore`, "PATCH").then((response) => {
                            if (response?.status !== 'Error') {
                                const getIsActiveColumn = row.querySelector('td:last-child');
                                table.cell(getIsActiveColumn).data('1').draw();
                            }
                        });
                    });
                });
                const grandAccessButton = document.querySelectorAll('button[id=access-row]');
                grandAccessButton.forEach(elem => {
                    elem.addEventListener('click', async function (event) {
                        const rowId = getRowId(getRow(event));
                        const modalOpen = document.querySelector('button[data-target="#retailer-access"]');
                        setRetailerId(rowId);
                        const retailerUsers = await getRetailerUsers(rowId);
                        const usersId = getUsersId(retailerUsers);
                        setSelectedUsers(usersId);
                        modalOpen.click();
                        grandAccess(rowId);
                        document.removeEventListener('click', grandAccess);
                    })
                })

                function modalRemoveAccept(element) {
                    document.addEventListener('click', function (event) {
                        if (event.target === document.getElementById('delete-btn')) {
                            const id = getRowId(element);
                            const cell = element.querySelector('td:last-child');
                            mainFetch(`retailers/${id}`, 'delete')
                                .then(response => {
                                    if (response?.status === 'Error') {
                                        setErrorWindow('Error', response.message, 'red');
                                    } else {
                                        table.cell(cell).data('0').draw()
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

            function setSelectedUsers($users) {
                $('#optionsUser').selectpicker('val', $users);
            }

            function getRetailerUsers(id) {
                return mainFetch(`retailers/${id}/users`, 'GET')
                    .then(response => {
                        if (response.status === "Success") {
                            return response.data;
                        } else {
                            return false;
                        }
                    }).catch(() => {
                        return false
                    });
            }

            function getUsersId(users) {
                let ids = [];
                for (const key in users) {
                    ids.push(users[key]['id']);
                }

                return ids;
            }

            function setRetailerId(id) {
                const input = document.getElementById('retailerId');
                input.setAttribute('value', id);
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


            function grandAccess(retailerId) {
                const buttonSave = document.getElementById('grand-access-save');
                buttonSave.addEventListener('click', function () {
                    const form = document.getElementById('grand-access-users');
                    const data = updatePrepareData(form);
                    const options = {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    };

                    mainFetch(`retailers/${retailerId}/grand-access`, 'PATCH', data.toString(), options);
                })
            }

            if ($.fn.DataTable.isDataTable('#table2')) {
                $('#table2').DataTable().clear().destroy(); // Очищення та знищення таблиці
            }
            initTable();
        });
    </script>
@endpush
