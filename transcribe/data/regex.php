<?php
function checkReport($report,$fileName) {

global $fileNameSuffix;
$content = urldecode($report);
$content = htmlspecialchars_decode($content);
$regex = '#<<(.*?)>>#';
$code2 = preg_match_all($regex, $content, $matches);
    if($code2 > 0)
    {
        return $fileName.$fileNameSuffix;
    }
    else{
        return $fileName;
    }
}