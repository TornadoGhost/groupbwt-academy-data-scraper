@extends('adminlte::auth.login')

@section('title', 'Login')
@section('adminlte_css_pre')
@stop

@section('auth_header')
    <h3 class="card-title float-none text-center">
        Login
    </h3>
@endsection

@section('auth_body')
    @error('errorLogin')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @enderror
    <form action="{{ route('login.store') }}" method="post" id="form" class="form">
        @csrf
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="row">
            <div class="col-5">
                <button type=submit
                        class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop
@push('js')
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";

        form.addEventListener('submit', function () {
            const formData = {
                email: form.elements.email.value,
                password: form.elements.password.value,
            };
            const option = {
                'Content-Type': 'application/json'
            };
            mainFetch('login', 'POST', JSON.stringify(formData), option);
        });
    </script>
@endpush
