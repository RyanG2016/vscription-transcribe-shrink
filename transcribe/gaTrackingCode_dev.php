<?php
include_once "data/parts/constants.php";
if(!$DEBUG){
    echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-XXXXXX\"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'UA-XXXXX');
        </script>";
}