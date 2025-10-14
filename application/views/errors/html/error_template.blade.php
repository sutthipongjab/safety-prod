<?php 
    defined('BASEPATH') OR exit('No direct script access allowed'); 
    if (!function_exists('get_instance')) {
        die("This file must be executed within the CodeIgniter framework.");
    }
    // echo base_url();
    // echo "<br>";
    // echo base_uri();
    // echo "<br>";
    // echo root_url();
    // echo "<br>";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Error</title>
    <link rel="stylesheet" href="{{ base_url('assets/dist/css/caveman.css') }}">
    <style type="text/css">
        #error-detail {
            width: 100vw;
            height: 100vh;
            margin-top: 10vh;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="error-detail">
        <h1>{{$heading}}</h1>
        <p>{{ $message }}</p>
    </div>
    <div class="text"><p>{{ $errorCode }}</p></div>
    <div class="container">
        <!-- caveman left -->
        <div class="caveman">
            <div class="leg">
            <div class="foot"><div class="fingers"></div></div>      
            </div>
            <div class="leg">
            <div class="foot"><div class="fingers"></div></div>      
            </div>
            <div class="shape">
            <div class="circle"></div>
            <div class="circle"></div>
            </div>
            <div class="head">
            <div class="eye"><div class="nose"></div></div>
            <div class="mouth"></div>
            </div>
            <div class="arm-right"><div class="club"></div></div>    
        </div>
        <!-- caveman right -->
        <div class="caveman">
            <div class="leg">
            <div class="foot"><div class="fingers"></div></div>      
            </div>
            <div class="leg">
            <div class="foot"><div class="fingers"></div></div>      
            </div>
            <div class="shape">
            <div class="circle"></div>
            <div class="circle"></div>
            </div>
            <div class="head">
            <div class="eye"><div class="nose"></div></div>
            <div class="mouth"></div>
            </div>
            <div class="arm-right"><div class="club"></div></div>    
        </div>
    </div>
    
</body>

</html>