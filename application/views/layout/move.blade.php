<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Move to another link.</title>
    <link rel="stylesheet" href="{{ base_url() }}assets/dist/css/tailwind.css?ver={{ $GLOBALS['version'] }}">
    <link rel="stylesheet" href="{{ base_url() }}assets/style/move.css">
    <script src="{{  $GLOBALS['script'] }}authen.bundle.js?ver={{ date('Ymdhis') }}" defer></script>
</head>

<body>
    <div class="draw-container">
        <div class="draw">
            <div class="flex flex-col items-center justify-center text-center">
                <h1 class="text-xl mt-5 px-8 w-[80%] font-bold md:text-3xl md:px-0 md:w-full md:font-normal">
                    We are taking you to your destination.
                </h1>
                <h1 class="text-md px-8 w-[80%] font-normal md:text-3xl md:px-0 md:w-full">
                    เรากำลังพาคุณไปยังปลายทาง...
                </h1>
            </div>
            <!-- Rocket -->
            <div class="rocket">
                <div class="window"></div>
            </div>

            <!-- Smoke -->
            <div class="smoke">
                <div class="one"></div>
                <div class="two"></div>
            </div>

            <!-- Stars -->
            <div class="stars">
                <div class="star one"></div>
                <div class="star two small"></div>
                <div class="star three small"></div>
                <div class="star four small"></div>
                <div class="star five"></div>
                <div class="star six samll"></div>
                <div class="star seven"></div>
                <div class="star eight small"></div>
            </div>
        </div>
    </div>
</body>

</html>
