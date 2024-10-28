@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Welcome')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Welcome')
@section('plugins.daterangepicker', true)

{{-- Content body: main page content --}}

@section('content_body')
    <div id="filters" class="container mb-2">
        <div class="row row-cols-4 gap-1">
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
            @if(!empty($retailers))
                <div class="col">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="retailer">Retailers</label>
                            <button type="button" id="reset-button-retailer"
                                    class="btn btn-secondary btn-sm rounded-circle p-0"
                                    style="width: 24px; height: 24px;" title="Reset retailer">
                                <i class="fas fa-undo" style="font-size: 14px;"></i>
                            </button>
                        </div>
                        <x-adminlte-select2 id="retailer" name="retailer">
                            <option selected>Select retailer...</option>
                            @foreach($retailers as $retailer)
                                <option value="{{ $retailer->id }}">{{ $retailer->name }}</option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
            @endif
            @if(!empty($products))
                <div class="col">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="product">Products</label>
                            <button type="button" id="reset-button-product"
                                    class="btn btn-secondary btn-sm rounded-circle p-0"
                                    style="width: 24px; height: 24px;" title="Reset product">
                                <i class="fas fa-undo" style="font-size: 14px;"></i>
                            </button>
                        </div>
                        <x-adminlte-select2 id="product" name="product">
                            <option selected>Select product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->title }} ({{ $product->id }})</option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="product">Manufacturer part number</label>
                            <button type="button" id="reset-button-mpn"
                                    class="btn btn-secondary btn-sm rounded-circle p-0"
                                    style="width: 24px; height: 24px;" title="Reset mpn">
                                <i class="fas fa-undo" style="font-size: 14px;"></i>
                            </button>
                        </div>
                        <x-adminlte-select2 id="mpn" name="mpn">
                            <option selected>Select manufacturer part number...</option>
                            @foreach($products as $product)
                                <option
                                    value="{{ $product->manufacturer_part_number }}">{{ $product->manufacturer_part_number }}</option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
            @endif
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
        <button type="button" id="reset-button-all" class="btn btn-secondary btn-sm" style="margin-left: 10px;">
            <span class="mr-2">Reset all filters</span>
            <i class="fas fa-undo"></i>
        </button>
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

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";

        let controller = new AbortController();

        $(document).ready(function () {
            let startDate = '', endDate = '', retailerId = '', productId = '', mpnData = '', userId = '';
            const dateRangeInput = document.getElementById('drPlaceholder');
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

                getMetrics(startDate, endDate, retailerId, productId, mpnData, userId);
            });


            changeFirstSelectElem('retailer');
            changeFirstSelectElem('product');
            changeFirstSelectElem('mpn');
            changeFirstSelectElem('user');

            const resetAll = document.getElementById('reset-button-all');
            resetAll.addEventListener('click', function () {
                const selectsIds = ['retailer', 'product', 'mpn', 'user'];
                selectsIds.forEach(id => {
                    resetSelectValue(id, '', false)
                });

                getMetrics(startDate, endDate, retailerId, productId, mpnData, userId);
            });

            function changeFirstSelectElem(id, startFetch = true) {
                $(`#${id}`).on('select2:select', function (e) {
                    setSelectValue(id, e.params.data.id, startFetch);
                });
                const resetRetailer = document.getElementById(`reset-button-${id}`);
                resetRetailer.addEventListener('click', function () {
                    resetSelectValue(id, startFetch);
                });
            }

            function resetSelectValue(id, startFetch) {
                const firstValue = getFirstSelectElemValue(id);
                $(`#${id}`).val(firstValue).trigger('change');
                setSelectValue(id, '', startFetch)
            }

            function setSelectValue(id, value, startFetch) {
                if (id === 'retailer') {
                    retailerId = value;
                } else if (id === 'product') {
                    productId = value;
                } else if (id === 'mpn') {
                    mpnData = value;
                } else if (id === 'user') {
                    userId = value;
                }

                if (startFetch) {
                    getMetrics(startDate, endDate, retailerId, productId, mpnData, userId);
                }
            }
        });

        function getFirstSelectElemValue(id) {
            return $(`#${id} option:first`).val();
        }

        function getMetrics(startDate = '', endDate = '', retailerId = '', productId = '', mpn = '', userId = '') {
            if (controller) {
                controller.abort('Filter changed');
            }
            controller = new AbortController();

            const list = document.getElementById('retailers-metrics');
            if (list.children[0]) {
                list.innerHTML = '';
                spinner.classList.remove('d-none');
                spinner.classList.add('d-flex');
            }

            console.log('All variables: ', {
                startDate,
                endDate,
                retailerId,
                productId,
                mpn,
                userId
            });

            const signal = controller.signal;
            mainFetch(
                `metrics?product_id=${productId}
                &manufacturer_part_number=${mpn}
                &retailer_id=${retailerId}
                &user_id=${userId}
                &start_date=${startDate}
                &end_date=${endDate}`,
                'GET', null, {}, signal)
                .then(response => {
                    if (response.status === "Success") {
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
