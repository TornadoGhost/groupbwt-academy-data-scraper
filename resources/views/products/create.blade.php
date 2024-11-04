@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Create products')
@section('content_header_title', 'Products')
@section('content_header_subtitle', 'Create')

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
        <div class="row">
            <div class="form-group col-md-6">
                <label>Retailers</label>
                <div class="input-group" id="retailers"></div>
            </div>
        </div>
        <label>Images</label>
        <input id="input-b5" name="images[]" type="file" class="mb-2" multiple>
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
                    }
                });
        });

        function addReturnButton() {
            const modal = document.getElementById('modalMin');
            const footer = modal.querySelector('.modal-footer');
            const button = `

                    <a href="{{route('products.index')}}"><button type="button" class="btn btn-default bg-green">Back to products</button></a>

            `;
            footer.insertAdjacentHTML("afterbegin", button)
        }

        addReturnButton();

        function addValidationMessage(id, message) {
            const element = document.getElementById(id);
            if (element) {
                element.classList.add('is-invalid');
                element.nextElementSibling.children[0].innerHTML = message;
            }
        }


        function removeValidationMessage() {
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('keypress', function (event) {
                    event.target.classList.remove('is-invalid');
                });
            })
        }

        removeValidationMessage();

        async function getRetailers() {
            let data;
            await mainFetch('retailers', 'GET')
                .then(response => {
                    if (response?.status === 'Success') {
                        data = response.data;
                    }
                })
            return data;
        }

        async function setRetailersData() {
            const retailers = await getRetailers();
            const retailersForm = document.getElementById('retailers');
            for (let i = 0; i < retailers.length; i++) {
                retailersForm.insertAdjacentHTML("beforeend", `
                    <div class="input-group mb-2">
                        <div class="input-group col-md-6 pl-0">
                        <input class="form-control" id="retailers.${i}.product_url" name="retailers[${i}][product_url]"
                        type="text" placeholder="Enter product url">
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                        </div>
                    <input disabled class="form-control" type="text" value="${retailers[i]['name']}" >
                    <input type="hidden" name="retailers[${i}][retailer_id]" value="${retailers[i]['id']}">
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                    </div>`);
            }
        }

        setRetailersData();

        $(document).ready(function () {
            // $("#input-b5").fileinput({showCaption: false, dropZoneEnabled: false});
            $("#input-b5").fileinput({
                /*initialPreview: [
                    "https://example.com/image1.jpg",
                    "https://example.com/image2.jpg"
                ],
                initialPreviewAsData: true, // дозволяє показувати зображення як дані, а не текст
                initialPreviewConfig: [
                    {caption: "Image 1", key: 1, url: "/site/file-delete", extra: {id: 1}},
                    {caption: "Image 2", key: 2, url: "/site/file-delete", extra: {id: 2}}
                ],*/
                // deleteUrl: "/site/file-delete", // загальний URL для видалення, якщо не задано у initialPreviewConfig
                overwriteInitial: false, // зберігає попередні зображення
                showRemove: false, // приховує загальну кнопку видалення
                showUpload: false, // приховує загальну кнопку завантаження
            });
        });
    </script>
@endpush
