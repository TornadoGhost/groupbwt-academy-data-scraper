@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Create products')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Products')
@section('content_header_subtitle_subtitle', 'Update')

{{-- Content body: main page content --}}

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
        @php
            $config = [
                "placeholder" => "Select multiple options...",
                "allowClear" => true,
            ];
        @endphp
        <div class="input-group flex-column" id="retailers">
            @if($product)
                @foreach($product->retailers as $key => $retailer)
                    <div class="d-flex align-items-center" data-retailer>
                        <x-adminlte-select2 id="retailers-select" name=`retailers[{{$key+10000}}][retailer_id]`
                                            label="Product URL"
                                            igroup-size="lg" data-placeholder="Select an option...">
                            <x-slot name="prependSlot">
                                <div class="input-group">
                                    <input class="form-control" id="retailers.${counter}.product_url"
                                           name="retailers[${counter}][product_url]"
                                           type="text" placeholder="Enter product url"
                                           value="{{ $retailer->pivot->product_url }}">
                                    <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                    <input class="form-control" id="retailers.${counter}.retailer_id" type="hidden">
                                    <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </x-slot>
                            <option>Select retailer</option>
                            @foreach($retailers as $r)
                                <option @if($retailer->name === $r->name) selected
                                        @endif value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </x-adminlte-select2>
                        <x-adminlte-button id="remove-button" class="btn-sm" type="reset" theme="outline-danger"
                                           icon="fas fa-lg fa-trash"/>
                    </div>
                @endforeach
            @endif
        </div>
        <span class="invalid-feedback" role="alert">
             <strong></strong>
        </span>
        <x-adminlte-button class="mb-3" theme="info" id="add-retailers" label="Add retailer"/>
        <x-adminlte-button class="d-block btn-flat" id="save-button" type="button" label="Save" theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>Product updated.</p>
    </x-adminlte-modal>
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

        /*$("#images").fileinput({
            theme: "explorer",
            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
            overwriteInitial: false,
            initialPreviewAsData: true,
            maxFileSize: 10000,
            removeFromPreviewOnError: true,
            initialPreview: [
                @foreach($product->images as $image)
                    "{{ asset($image->path) }}",
                @endforeach()
            ],
            initialPreviewDownloadUrl: 'https://picsum.photos/id/{key}/1920/1080'
        });*/

        let counter = 0;

        const retailersData = await getData();
        const options = retailersData.map(retailer =>
            `
    <option value="${retailer.id}">${retailer.name}</option>`
        ).join('');

        const addRetailersBtn = document.getElementById('add-retailers');
        addRetailersBtn.addEventListener('click', function () {
            const element = `
    <div class="d-flex align-items-center" data-retailer>
        <x-adminlte-select2 id="retailers-select" name="retailers[${counter}][retailer_id]" label="Product URL"
                            igroup-size="lg" data-placeholder="Select an option...">
            <x-slot name="prependSlot">
                <div class="input-group">
                    <input class="form-control" id="retailers.${counter}.product_url"
                           name="retailers[${counter}][product_url]"
                           type="text" placeholder="Enter product url">
                    <span class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                    <input class="form-control" id="retailers.${counter}.retailer_id" type="hidden">
                    <span class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                </div>
            </x-slot>
            <option>Select retailer</option>
            ${options}
        </x-adminlte-select2>
        <x-adminlte-button id="remove-button" class="btn-sm" type="reset" theme="outline-danger"
                           icon="fas fa-lg fa-trash"/>
    </div>
    `;
            retailers.insertAdjacentHTML('beforeend', element);
            retailers.classList.remove('is-invalid');
            addRetailersBtn.classList.remove('btn-danger');

            counter += 1;
        });

        document.addEventListener('click', function (event) {
            const removeButton = event.target.closest('#remove-button');
            if (removeButton) {
                removeButton.closest('div[data-retailer]').remove();
            }
        })

        const submitButton = document.getElementById('save-button');
        submitButton.addEventListener('click', function (event) {
            event.preventDefault();

            const form = document.getElementById('product-update');
            let formData = new FormData(form);
            const urlEncodedData = new URLSearchParams();

            formData.forEach((value, key) => {
                urlEncodedData.append(key, value);
                console.log(key, value);
            });

            const options = {
                'Content-Type': 'application/x-www-form-urlencoded',
            };

            mainFetch('products/{{ $product->manufacturer_part_number }}', 'PATCH', urlEncodedData.toString(), options)
                .then(response => {
                    if (response.errors) {
                        const errors = response.errors;
                        console.log(errors);

                        const errorInputName = Object.entries(errors);
                        errorInputName.forEach(value => {
                            addValidationMessage(value[0], value[1][0])
                        })
                    } else {
                        document.getElementById('modal-open-btn').click();
                    }
                });
        });

        async function getData() {
            let retailersData;
            await mainFetch('retailers', 'GET')
                .then((response) => {
                    retailersData = response.data;
                });

            return retailersData;
        }

        function addValidationMessage(id, message) {
            const element = document.getElementById(id);
            if (element) {
                element.classList.add('is-invalid');
                element.nextElementSibling.children[0].innerHTML = message;
            }
        }

        removeValidationMessage();

        function removeValidationMessage() {
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('keypress', function (event) {
                    event.target.classList.remove('is-invalid');
                });
            })
        }
    </script>
@endpush
