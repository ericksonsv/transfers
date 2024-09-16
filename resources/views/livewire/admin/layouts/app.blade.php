<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name') }} | {{ trans('Service Order') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            window.addEventListener("load", window.print());
        </script>
        <style>
            @media print {
                .no-printme {
                    display: none;
                }
    
                .printme {
                    display: block;
                }
    
                body {
                    line-height: 1.2;
                }
            }
    
            @page {
                /* size: A4 portrait;
                counter-increment: page; */
            }
    
        </style>
    </head>
    <body>
        {{ $slot }}
    </body>
</html>