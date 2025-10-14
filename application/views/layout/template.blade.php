

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport"    content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="base_url"    content="{{ base_url() }}">
    <meta name="base_uri"    content="{{ ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http").'://' . $_SERVER['HTTP_HOST'] }}">
    <meta name="base_scheme" content="{{ $GLOBALS['scheme'] }}">
    <meta name="theme-color" content="#C0C0C0">
    <link rel="manifest" href="{{ base_url() }}manifest.json?ver={{ date('YmdHis') }}">
    <link rel="shortcut icon" href="{{ base_url() }}assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="{{ base_url() }}assets/images/favicon.ico">
    <link rel="apple-touch-startup-image" href="{{ base_url() }}assets/images/safety_icon.png">
    <meta name="app_version" content="{{ $GLOBALS['version'] }}">
    {{-- <link rel="stylesheet" href="{{ $GLOBALS['cdn'] }}icofont/icofont.min.css"> --}}
    <link rel="stylesheet" href="{{ $GLOBALS['cdn'] }}icofont/2025.01.30/icofont.min.css">
    <link rel="stylesheet" href="{{ $GLOBALS['style'] }}tailwind.css?ver={{ date('Ymdhis') }}">
    <title>AMEC Safety</title>
    @yield('styles')
    {{-- <script src="{{ base_url() }}script.js?ver={{ date('Ymdhis') }}"></script> --}}
</head>

<body class="flex flex-col min-h-screen bg-skin-body" menuTitle="{{ $title ?? '' }}">
    @include('layout/preload')
    <div 
        class="user-info-data" 
        sempno="{{$_SESSION['user']->SEMPNO}}" 
        sec="{{$_SESSION['user']->SSEC}}" 
        dept="{{$_SESSION['user']->SDEPT}}"
        div="{{$_SESSION['user']->SDIV}}"
        SSECCODE="{{$_SESSION['user']->SSECCODE}}" 
        SDEPCODE="{{$_SESSION['user']->SDEPCODE}}"
        SDIVCODE="{{$_SESSION['user']->SDIVCODE}}"
        SPOSCODE="{{$_SESSION['user']->SPOSCODE}}"
        GROUP_CODE="{{$_SESSION['group']->GROUP_CODE}}"
    ></div>
    <div class="flex-grow">
            
            @include('layout/sidebar')
            @include('layout/navbar')
            <div id="contents" class="flex flex-col md:ml-64  lg:ml-80 transition-all duration-300 pt-16">
                {{-- <h1 class="pl-5 my-5 text-2xl ">จัดการพื้นที่</h1> --}}
                <h2 class="px-5 my-5 font-bold text-3xl" id="topic">
                    <div class="skeleton h-8 w-full"></div>
                </h2>
                @yield('contents')
            </div>
            {{-- <div class="flex"> --}}

                {{-- <div class="px-5 min-h-[calc(100vh-68px)] z-10"> --}}
            {{-- </div> --}}

            {{-- Pattern --}}
            {{-- <div class="absolute z-0 bottom-0 right-0 h-[750px] w-[750px] overflow-hidden">
                <div
                    class="absolute mask mask-triangle-3 bottom-[-10px] right-[-105px] rotate-45 pattern-dots pattern-gray-500 pattern-bg-transparent pattern-size-2 pattern-opacity-20 w-full h-full">
                </div>
            </div> --}}


        {{-- <input type="checkbox" id="loading-box" class="modal-toggle" checked /> --}}
        {{-- <div class="modal" role="dialog">
            <div class="loader"></div>
        </div> --}}
    </div>
    {{-- @include('layout/footer') --}}
    <script src="{{  $GLOBALS['script'] }}app.bundle.js?ver={{ date('Ymdhis') }}"></script>
    @yield('scripts')
</body>

</html>
