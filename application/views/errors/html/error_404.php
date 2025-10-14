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
    <style type="text/css">
    html,
    body {
        font-family: Helvetica, Arial, sans-serif;
        min-width: 90vw;
        min-height: 90vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #container {
        /* background-image: url(https://amecweb1.mitsubishielevatorasia.co.th/cdn/theme/assets/image/dribbble_1.gif); */
        background-image: url(<?php echo $image; ?>);
        background-position: center;
        background-repeat: no-repeat;
        height: 500px;
        width: 100%;
    }

    #container h1,
    #container p {
        text-align: center;
        color: #697a8d;
    }
    </style>
</head>

<body>
    <div id="container">
        <h1><?php echo $heading; ?></h1>
        <p><?php echo $message; ?></p>
    </div>
</body>

</html>