@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Update product')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Products')
@section('content_header_subtitle_subtitle', 'Update')
@section('plugins.inputFileKrajee', true)

{{-- Content body: main page content --}}

@section('content_body')
    <form id="product-update">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="title">Title</label>
                <div class="input-group">
                    <input disabled class="form-control" id="title" name="title"
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
                    <input disabled class="form-control" id="manufacturer_part_number" name="manufacturer_part_number"
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
                    <input disabled class="form-control" id="pack_size" name="pack_size"
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
        <input disabled id="input-b5" name="images[]" type="file" class="mb-2" multiple>
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

        const product = await getProduct('{{ $product->id }}');

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
                        <input disabled class="form-control" id="retailers.${i}.product_url" name="retailers[${i}][product_url]" value="${productUrl}"
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
                    key: id,
                })),

                showRemove: false,
                showUpload: false,
                showClose: false,
            });
        });
    </script>
@endpush
