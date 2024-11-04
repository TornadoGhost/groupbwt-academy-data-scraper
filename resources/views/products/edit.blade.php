@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Edit product')
@section('content_header_title', 'Products')
@section('content_header_subtitle', 'Edit')
@section('plugins.inputFileKrajee', true)

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
        <div class="row">
            <div class="form-group col-md-6">
                <label>Retailers</label>
                <div class="input-group" id="retailers"></div>
            </div>
        </div>
        <input id="input-b5" name="images[]" type="file" class="mb-2">
        <x-adminlte-button class="d-block btn-flat rounded mt-2" id="save-button" type="button" label="Save"
                           theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>Product updated.</p>
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
        import {updatePrepareData} from "{{ asset('js/updatePrepareData.js') }}";


        const product = await getProduct('{{ $product->id }}');

        const submitButton = document.getElementById('save-button');
        submitButton.addEventListener('click', function (event) {
            event.preventDefault();

            const form = document.getElementById('product-update');
            const data = updatePrepareData(form);

            mainFetch('products/{{ $product->id }}', 'PATCH', data.toString())
                .then(response => {
                    if (response.errors) {
                        const errors = response.errors;
                        const errorInputName = Object.entries(errors);
                        errorInputName.forEach(value => {
                            addValidationMessage(value[0], value[1][0])
                        })
                    } else {
                        document.getElementById('modal-open-btn').click();
                    }
                });
        });

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

        function addReturnButton() {
            const modal = document.getElementById('modalMin');
            const footer = modal.querySelector('.modal-footer');
            const button = `

                    <a href="{{route('products.index')}}"><button type="button" class="btn btn-default bg-green">Back to products</button></a>

            `;
            footer.insertAdjacentHTML("afterbegin", button)
        }

        addReturnButton();

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

        async function getProduct(id) {
            let data;
            await mainFetch(`products/${id}`, 'GET')
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
            retailers.forEach((retailer, i) => {
                const existingRetailer = product.retailers.find(pr => pr.name === retailer.name);
                const productUrl = existingRetailer ? existingRetailer.product_url : '';
                retailersForm.insertAdjacentHTML("beforeend", `
                <div class="input-group mb-2">
                    <div class="input-group col-md-6 pl-0">
                        <input class="form-control" id="retailers.${i}.product_url" name="retailers[${i}][product_url]" value="${productUrl}"
                        type="text" placeholder="Enter product url">
                        <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <input disabled class="form-control" type="text" value="${retailer.name}" >
                    <input type="hidden" name="retailers[${i}][retailer_id]" value="${retailer.id}">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
    `);
            });
        }setRetailersData();

        $(document).ready(async function () {
            const baseUrl = 'http://localhost/';
            const imagesPath = product.images.map(item => `${baseUrl}${item.path}`);
            const imagesId = product.images.map(item => item.id);
            $("#input-b5").fileinput({
                initialPreview: imagesPath,
                initialPreviewAsData: true,
                initialPreviewConfig: imagesId.map((id, index) => ({
                    caption: `Image ${index + 1}`,
                    key: id, // Використання ID для ключа
                })),
                showRemove: false, // Показує кнопку видалення
                showUpload: false, // Приховує загальну кнопку завантаження
            });
            const deleteImageButtons = document.querySelectorAll('button[type=button][title="Remove file"]');
            deleteImageButtons.forEach(item => {
                item.addEventListener('click', function(event) {
                    const imageId = event.target.closest('button').getAttribute('data-key');
                    const elem = event.target.closest('div.krajee-default');
                    elem.remove();
                    mainFetch(`images/${imageId}`, 'DELETE')
                        .then(response => console.log(response))
                });
            })
        });
    </script>
@endpush
