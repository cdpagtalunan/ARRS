<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ARRS</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>
            .wrapper, body, html{
                min-height: auto !important;
            }
        </style>
    </head>
    <body class="hold-transition sidebar-mini" id="app">
            <router-view/>

        <script src="{{ asset('public/js/app.js') }}"></script>
    </body>
</html>
