@extends('layouts.app')

{{-- Customize layout sections --}}
@section('adminlte_css_pre')
@stop

@section('subtitle', 'Update user')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'User')
@section('content_header_subtitle_subtitle', 'Update')

{{-- Content body: main page content --}}
@section('content_body')
    <form id="user-update">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="username">Username</label>
                <div class="input-group">
                    <input class="form-control" id="username" name="username"
                           type="text" placeholder="Enter username" value="{{ $user['username'] }}">
                    <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <div class="input-group">
                    <input class="form-control" id="email" name="email"
                           type="text" placeholder="Enter user email" value="{{ $user['email'] }}">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="password">Password</label>
                <div class="input-group">
                    <input class="form-control" id="password" name="password"
                           type="text" placeholder="Enter user password">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="region_id">Region</label>
                <x-adminlte-select2 name="region_id">
                    <option selected disabled>Select region</option>
                    @foreach($regions as $region)
                        <option @if($user['region_id'] === $region->id) selected @endif value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </x-adminlte-select2>
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
            </div>
        </div>
        <x-adminlte-button class="d-block btn-flat" id="save-button" type="button" label="Save" theme="success"
                           icon="fas fa-lg fa-save"/>
    </form>
    <x-adminlte-modal id="modalMin" title="Success" theme="green">
        <p>User was updated.</p>
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

        let counter = 0;

        const submitButton = document.getElementById('save-button');
        submitButton.addEventListener('click', function (event) {
            event.preventDefault();

            const form = document.getElementById('user-update');
            const data = updatePrepareData(form);

            mainFetch('users/{{ $user['id'] }}', 'PATCH', data.toString())
                .then(response => {
                    console.log(response)
                    if (response?.errors) {
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