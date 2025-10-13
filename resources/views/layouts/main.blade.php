<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <title>{{ (($titlePage ?? $title ?? false) ? ($titlePage ?? $title) . ' - ' : '') . config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('bootstrap/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="min-vh-100 bg-light pb-2">
        @include('layouts.navigation')

        @if ($titlePage ?? $title ?? false)
            <header class="bg-white shadow-sm">
                <div class="container py-4">
                    <h2 class="h5 m-0">
                        {{ $titlePage ?? $title }}
                    </h2>
                </div>
            </header>
        @endif

        <main class="container">
            <div class="my-5">
                {{-- Prioritaskan section 'content' (normal Blade). Kalau nggak ada, pakai $slot (component style). --}}
                @hasSection('content')
                    @yield('content')
                @elseif (isset($slot))
                    {{ $slot }}
                @endif
            </div>
        </main>
    </div>

    <x-modal-delete />

    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // safety check supaya nggak error kalau modal nggak ada
        const deleteModal = document.getElementById('deleteModal')
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget
                const url = button.getAttribute('data-url')
                const deleteForm = deleteModal.querySelector('form')
                deleteForm.setAttribute('action', url)
            });
        }
    </script>
</body>
</html>