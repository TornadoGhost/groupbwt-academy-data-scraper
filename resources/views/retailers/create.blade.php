{{--TODO admin views, can be deleted or saved for future, because not used right now--}}
@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Create retailer')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Retailer')
@section('content_header_subtitle_subtitle', 'Create')

{{-- Content body: main page content --}}

@section('content_body')
    <form id="retailer-create">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="name">Name</label>
                <div class="input-group">
                    <input class="form-control" id="name" name="name"
                           type="text" placeholder="Enter retailer name">
                    <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="reference">Reference</label>
                <div class="input-group">
                    <input class="form-control" id="reference" name="reference"
                           type="text" placeholder="Enter retailer reference">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="currency">Currency</label>
                <div class="input-group">
                    <input class="form-control" id="currency" name="currency"
                           type="text" placeholder="Enter retailer currency">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="logo_path">Logo url</label>
                <div class="input-group">
                    <input class="form-control" id="logo_path" name="logo_path"
                           type="text" placeholder="Enter logo url">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <x-adminlte-button class="d-block btn-flat" id="save-button" type="button" label="Save" theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>Retailer was created.</p>
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

        const submitButton = document.getElementById('save-button');
        submitButton.addEventListener('click', function (event) {
            event.preventDefault();

            const form = document.getElementById('retailer-create');
            let formData = new FormData(form);

            mainFetch('retailers', 'POST', formData)
                .then(response => {
                    if (response?.errors) {
                        const errors = response.errors;

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
    </script>
@endpush
