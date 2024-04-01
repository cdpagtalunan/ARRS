<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ARRS</title>
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
        <link rel="stylesheet" href="{{ asset('public/template/fontawesome/css/all.min.css') }}">

        <style>
            .wrapper, body, html{
                min-height: auto !important;
            }
            .tableRecon tbody td{
                padding: 4px 4px;
                margin: 1px 1px;
                font-size: 15px;
                /* text-align: center; */
                vertical-align: middle;
            }
            .tableRecon thead th:first-child{
                width: 10% !important;
            }
            .tableRecon thead th{
                padding: 4px 4px;
                margin: 1px 1px;
                font-size: 16px;
                text-align: center !important;
                vertical-align: middle;
            }
        </style>
        
        <link rel="stylesheet" href="{{ asset('public/css/app.css') }}">
    </head>
    <body class="hold-transition sidebar-mini" id="app">
            <router-view/>

        <script src="{{ asset('public/js/app.js') }}"></script>
    </body>
</html>
