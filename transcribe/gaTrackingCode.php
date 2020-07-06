<?php
include_once "data/parts/constants.php";
if(!$DEBUG){
    echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-115629565-1\"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'UA-115629565-1');
        </script>";
}