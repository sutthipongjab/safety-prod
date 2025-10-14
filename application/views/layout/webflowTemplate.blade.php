

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport"    content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="base_url"    content="{{ base_url() }}">
    <meta name="base_uri"    content="{{ $GLOBALS['uri'] }}">
    <meta name="base_scheme" content="{{ $GLOBALS['scheme'] }}">
    <meta name="theme-color" content="#C0C0C0">
    <link rel="manifest" href="{{ base_url() }}manifest.json?ver={{ date('YmdHis') }}">
    <link rel="shortcut icon" href="{{ base_url() }}assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="{{ base_url() }}assets/images/favicon.ico">
    <link rel="apple-touch-startup-image" href="{{ base_url() }}assets/images/safety_icon.png">
    <meta name="app_version" content="{{ $GLOBALS['version'] }}">
    <link rel="stylesheet" href="{{ $GLOBALS['cdn'] }}icofont/icofont.min.css">
    <link rel="stylesheet" href="{{ $GLOBALS['style'] }}tailwind.css?ver={{ date('Ymdhis') }}">
    <title>AMEC Safety</title>
    @yield('styles')
</head>

<body class="flex flex-col min-h-screen bg-skin-body" menuTitle="{{ $title ?? '' }}">
    <div 
        class="user-info-data" 
    ></div>
    @include('layout/preload')
    <div class="flex-grow">
            
            <div id="contents" class="flex flex-col  transition-all duration-300 ">
                {{-- <h2 class="px-5 my-5 font-bold text-3xl" id="topic">
                    <div class="skeleton h-8 w-full"></div>
                </h2> --}}
                @yield('contents')
            </div>
    </div>
    <script src="{{  $GLOBALS['script'] }}app.bundle.js?ver={{ date('Ymdhis') }}"></script>
    @yield('scripts')
</body>

</html>
