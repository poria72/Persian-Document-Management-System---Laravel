<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('settings.system_title')}}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #575e62;
            font-family: 'B Nazanin', 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
            background-image: url('background.png');
            background-repeat: no-repeat;
            background-size: auto;
            background-position: bottom;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 5px 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
            border: 1px solid #636b6f;
            border-radius: 10px;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ route('admin.dashboard') }}">داشبورد</a>
            @else
                <a href="{{ route('login') }}">ورود</a>
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">
            {{config('settings.system_title')}}
        </div>

        {{-- <div class="links">
            <blockquote>
                {{$quotes}}
            </blockquote>
        </div> --}}
    </div>
</div>
</body>
</html>
