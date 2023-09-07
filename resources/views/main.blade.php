<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{env('APP_NAME')}}</title>
    @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
    @if(!empty($arr))
    @foreach($arr as $key => $value)
        
        <p>{{ $key }}: {{ $value }}</p>
    @endforeach
    @endif
    <div id="app"></div>
    @vite('resources/js/app.js')
</body>
</html>