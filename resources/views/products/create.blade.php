@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Create products')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Products')
@section('content_header_subtitle_subtitle', 'Create')

{{-- Content body: main page content --}}

@section('content_body')
    <form id="product-create">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="title">Title</label>
                <div class="input-group">
                    <input class="form-control" id="title" name="title"
                           type="text" placeholder="Enter title name of product">
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
                           type="text" placeholder="Enter product manufacturer part number">
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
                           type="text" placeholder="Enter product pack size">
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
        <div class="input-group flex-column" id="retailers"></div>
        <span class="invalid-feedback" role="alert">
             <strong></strong>
        </span>
        <x-adminlte-button class="mb-3" theme="info" id="add-retailers" label="Add retailer"/>
        <div class="row col-md-6">
            <x-adminlte-input-file id="product-images" name="images[]" label="Upload images"
                                   placeholder="Choose multiple images..." igroup-size="md" legend="Choose"
                                   multiple>
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fas fa-file-upload"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-file>
        </div>
        <x-adminlte-button class="d-block btn-flat" id="save-button" type="submit" label="Save" theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>Product was created.</p>
    </x-adminlte-modal>
    <x-adminlte-button class="d-none" id="modal-open-btn" label="Open Modal" data-toggle="modal"
                       data-target="#modalMin"/>
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

        let counter = 0;

        const retailersData = await getData();
        const options = retailersData.map(retailer =>
            `<option value="${retailer.id}">${retailer.name}</option>`
        ).join('');

        const addRetailersBtn = document.getElementById('add-retailers');
        addRetailersBtn.addEventListener('click', function () {
            const element = `
                    <div class="d-flex align-items-center" data-retailer>
                    <x-adminlte-select2 id="retailers-select" name="retailers[${counter}][retailer_id]" label="Product URL"
                                    igroup-size="lg" data-placeholder="Select an option...">
                    <x-slot name="prependSlot">
                        <div class="input-group">
                            <input class="form-control" id="retailers.${counter}.product_url" name="retailers[${counter}][product_url]"
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
                <x-adminlte-button id="remove-button" class="btn-sm" type="reset" theme="outline-danger" icon="fas fa-lg fa-trash"/>
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

            const form = document.getElementById('product-create');
            let formData = new FormData(form);

            mainFetch('products', 'POST', formData)
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
                        form.reset();
                        const resetButtons = document.querySelectorAll('button[type=reset]');
                        resetButtons.forEach((button) => {
                            button.click();
                        });
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
