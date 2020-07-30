<?php
include('data/parts/head.php');
require_once ('rtf3/src/HtmlToRtf.php');
require_once('data/regex.php');
include ('data/parts/constants.php');

if(isset($_POST))
{
//	alert('check');
    if(isset($_POST['jobNo']))
    {
        $report = '<b>'.'Job Number: ' .'</b>'. $_POST['jobNo'] .'<br/>';
        $report = $report . '<b>'.'Author Name: ' .'</b>'. $_POST['authorName'].'<br/>';
        $report = $report . '<b>'.'Typist Name: ' .'</b>'.$_POST['TypistName'].'<br/>';
        $report = $report . '<b>'.'Job Type: ' .'</b>'.$_POST['jobType'].'<br/>';
        $report = $report . '<b>'.'Date Dictated: ' .'</b>'.$_POST['DateDic'].'<br/>';
        $report = $report. '<b>'.'Date Transcribed: ' .'</b>' .$_POST['DateTra'].'<br/>';
		$report = $report . '<b>'.'Comments: ' .'</b>'.$_POST['comments'].'<br/>';
		
		$report = $report.'<br/>';
		$report = $report.'<br/>';
        $report = $report . $_POST['report'];

        $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
//        $htmlToRtfConverter->getRTFFile();
        $convertedRTF = trim($htmlToRtfConverter->getRTF());
		echo($convertedRTF);
    }
}
else
{
	echo "Looks like JobNo is empty";

}
