@extends('layouts.app')

@section('subtitle', 'Metrics')
@section('content_header_title', 'Metrics')
@section('plugins.daterangepicker', true)

@section('content_body')
    <div>
        <x-adminlte-button class="mb-2" id="export-btn" label="Export metrics" theme="primary"/>
    </div>
    <div id="filters" class="container mb-2">
        <div class="row row-cols-3 gap-1">
            <div class="col">
                <x-adminlte-date-range name="drPlaceholder" placeholder="Select a date range..." label="Date">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="far fa-lg fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-date-range>
                @push('js')
                    <script>$(() => $("#drPlaceholder").val(''))</script>
                @endpush
            </div>
            <div class="col">
                <div class="form-group">
                    @php
                        $config = [
                            "placeholder" => "Select multiple options...",
                            "allowClear" => true,
                        ];
                    @endphp
                    <x-adminlte-select2 id="retailers" name="retailers[]" igroup-size="md" label="Retailers"
                                        :config="$config" multiple>
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-red">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select2>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    @php
                        $config = [
                            "placeholder" => "Select multiple options...",
                            "allowClear" => true,
                        ];
                    @endphp
                    <x-adminlte-select2 id="products" name="products[]" igroup-size="md" label="Products"
                                        :config="$config" multiple>
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-red">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select2>
                </div>
            </div>
            @if(auth()->user()->isAdmin)
                @if($users)
                    <div class="col">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="user">Users</label>
                                <button type="button" id="reset-button-user"
                                        class="btn btn-secondary btn-sm rounded-circle p-0"
                                        style="width: 24px; height: 24px;" title="Reset user">
                                    <i class="fas fa-undo" style="font-size: 14px;"></i>
                                </button>
                            </div>
                            <x-adminlte-select2 id="user" name="user">
                                <option selected>Select user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
    <div class="container">
        <div id="spinner" class="d-none align-items-center">
            <strong>Metrics Loading...</strong>
            <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
        </div>
        <ul id="retailers-metrics" class="row row-cols-2 gap-3 list-unstyled">
        </ul>
    </div>
    <x-adminlte-modal id="errors-modal" title="Error" theme="red">
        Error
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="error-modal-button" label="Error" data-toggle="modal"
                       data-target="#errors-modal"/>
@stop

@push('js')
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";
        import {exportData} from "{{ asset('js/exportData.js') }}";

        let controller = new AbortController();

        async function getMetricsData(tableName) {
            let data;
            await mainFetch(`metrics/${tableName}`, 'GET').then(response => {
                data = response.data
            });

            return data;
        }

        async function seedProducts() {
            const productsList = await getMetricsData('products');
            productsList.forEach(elem => {
                products.insertAdjacentHTML('beforeend',
                    `<option value="${elem.id}">${elem.title} (${elem.manufacturer_part_number})</option>`
                );
            })
        }

        seedProducts();

        async function seedRetailers() {
            const retailersList = await getMetricsData('retailers');
            retailersList.forEach(elem => {
                retailers.insertAdjacentHTML('beforeend',
                    `<option value="${elem.id}">${elem.name}</option>`
                );
            })
        }

        seedRetailers();

        $(document).ready(function () {
            let startDate = '',
                endDate = '',
                retailers = [],
                products = [],
                userId = '';

            const dateRangeInput = document.getElementById('drPlaceholder');
            // TODO make receive first and last date from api, then move js
            const today = moment('{{ $firstDate }}');
            const lastDate = moment('{{ $lastDate }}');

            $(dateRangeInput).daterangepicker({
                startDate: lastDate,
                endDate: lastDate,
                minDate: today,
                maxDate: lastDate,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $(dateRangeInput).on('apply.daterangepicker', function (ev, picker) {
                const firstDay = picker.startDate.format('YYYY-MM-DD');
                const lastDay = picker.endDate.format('YYYY-MM-DD');

                startDate = firstDay;
                endDate = lastDay;

                getMetrics(startDate, endDate, retailers, products, userId);
            });

            $(`#retailers`)
                .on('select2:select', function (e) {
                    retailers.push(e.params.data.id);
                    getMetrics(startDate, endDate, retailers, products, userId);
                })
                .on('select2:clear', function () {
                    retailers = [];
                    getMetrics(startDate, endDate, retailers, products, userId);
                })
                .on('select2:unselect', function (e) {
                    retailers = retailers.filter(id => id !== e.params.data.id)
                    getMetrics(startDate, endDate, retailers, products, userId);
                });

            $(`#products`)
                .on('select2:select', function (e) {
                    products.push(e.params.data.id);
                    getMetrics(startDate, endDate, retailers, products, userId);
                })
                .on('select2:clear', function () {
                    products = [];
                    getMetrics(startDate, endDate, retailers, products, userId);
                })
                .on('select2:unselect', function (e) {
                    products = products.filter(id => id !== e.params.data.id)
                    getMetrics(startDate, endDate, retailers, products, userId);
                });

            const exportBtn = document.getElementById('export-btn');
            exportBtn.addEventListener('click', function () {
                const successAlert =
                    `<x-adminlte-alert id="success-alert" class="position-absolute top-0 end-0 m-3 bg-green" style="right: 0;" icon="fa fa-lg fa-thumbs-up" title="Started" dismissable>
                        Export started! Wait for a notification.
                    </x-adminlte-alert>`;
                const url = metricsUrl('metrics/export', startDate, endDate, retailers, products, userId);
                exportData(url, successAlert);
            })

            getMetrics();
        });

        function disabledExportBtn() {
            document.getElementById('export-btn').setAttribute('disabled', '');
        }
        function enabledExportBtn() {
            document.getElementById('export-btn').removeAttribute('disabled');
        }

        function metricsUrl(startUrl, startDate = '', endDate = '', retailers = [], products = [], userId = '') {
            return `${startUrl}?` + retailers.map(id => `retailers[]=${id}`).join("&")
                + products.map(id => `&products[]=${id}`).join("&") +
                `&user_id=${userId}
                &start_date=${startDate}
                &end_date=${endDate}`;
        }

        function getMetrics(startDate = '', endDate = '', retailers = [], products = [], userId = '') {
            if (!document.getElementById('export-btn').getAttribute('disabled')) disabledExportBtn();
            if (controller) {
                controller.abort('Filter changed');
            }
            controller = new AbortController();

            spinner.classList.remove('d-none');
            spinner.classList.add('d-flex');

            const list = document.getElementById('retailers-metrics');
            if (list.children[0]) {
                list.innerHTML = '';
                spinner.classList.remove('d-none');
                spinner.classList.add('d-flex');
            }

            const signal = controller.signal;
            const url = metricsUrl('metrics', startDate, endDate, retailers, products, userId);
            mainFetch(url, 'GET', null, {}, signal)
                .then(response => {
                    if (response.status === "Success") {
                        if (response.data.length !== 0) {
                            enabledExportBtn();
                        }

                        spinner.classList.remove('d-flex');
                        spinner.classList.add('d-none');

                        for (const key in response.data) {
                            const liElement = document.createElement("li");
                            liElement.classList.add('col');
                            liElement.innerHTML = `
                            <li class="col">
                                <x-adminlte-card title="${response.data[key].retailer_name}" theme="teal">
                                    <p>Average product rating: <strong>${response.data[key].average_product_rating}</strong></p>
                                    <p>Average product price: <strong>${response.data[key].average_product_price}</strong></p>
                                    <p>Average images per product: <strong>${response.data[key].average_images_per_product}</strong></p>
                                </x-adminlte-card>
                            </li>
                        `;
                            list.append(liElement);
                        }
                    } else {
                        const modal = document.getElementById('errors-modal');
                        const modalBody = modal.getElementsByClassName('modal-body')[0];
                        if (response.message) {
                            modalBody.innerHTML = response.message;
                        } else {
                            modalBody.innerHTML = "Some error happened, try again later"
                        }
                        const button = document.getElementById('error-modal-button');
                        button.click();

                    }
                }).catch(exception => {
                if (signal.aborted) {
                    const {reason} = signal;
                } else console.log(`Fetch failed with exception: ${exception}`);
            });
        }
    </script>
@endpush
