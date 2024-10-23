@extends('adminlte::auth.login')

@section('adminlte_css_pre')
@stop

@section('auth_header')
    <h3 class="card-title float-none text-center">
        Login
    </h3>
@endsection

@section('auth_body')
    <form id="form" class="form">
        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            <span class="invalid-feedback" role="alert">
                <strong id="email-error"></strong>
            </span>
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>


            <span class="invalid-feedback" role="alert">
                <strong id="password-error"></strong>
            </span>
        </div>

        {{-- Login field --}}
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

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = {
                email: form.elements.email.value,
                password: form.elements.password.value,
            };

            mainFetch('login', 'POST', JSON.stringify(formData))
                .then(({data}) => {
                    if (data.errors) {
                        const {email, password} = data.errors;
                        form.email.classList.add("is-invalid");
                        form.password.classList.add("is-invalid");
                        document.getElementById('email-error').innerText = email;
                        document.getElementById('password-error').innerText = password[0];
                    } else {
                        localStorage.setItem('accessToken', data.token);
                        window.location.href = "{{ route('home') }}";
                    }
                })
                .catch(error => console.log(error));
        });
    </script>
@endpush
