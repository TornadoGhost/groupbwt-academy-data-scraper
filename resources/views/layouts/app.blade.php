@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('content_top_nav_right')
@endsection

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        @yield('subtitle')
    @endif
@stop

{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        <a href="{{ config('app.company_url', '#') }}">
            {{ config('app.company_name', 'My company') }}
        </a>
    </strong>
@stop

{{-- Add common Javascript/Jquery code --}}

@push('js')
    <script type="module">
        import {mainFetch} from "{{ asset('js/mainFetch.js') }}";

        logout.addEventListener('click', function () {
            mainFetch('logout', 'POST');
        });

        const notification = document.getElementById('my-notification');
        document.addEventListener('click', function(event) {
            if (event.target === notification || event.target.closest('#my-notification') === notification) {
                mainFetch('notifications/mark-all-read', 'PATCH').then(response => {
                    if (response.status === 'Success') {
                        notification.querySelector('span.badge.navbar-badge').remove();
                    }
                })

            }
        });
    </script>
 @endpush

{{-- Add common CSS customizations --}}

@push('css')
    <style type="text/css">
        .content-wrapper {
            height: auto;
        }
    </style>
@endpush
