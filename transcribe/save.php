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
		echo "The report body so far is: " . $report;

        $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
//        $htmlToRtfConverter->getRTFFile();
        $convertedRTF = trim($htmlToRtfConverter->getRTF());

        $filename=$_POST['jobNo']."FILE";
//        header("Content-Type: application/rtf");
//        header("Content-Transfer-Encoding: Binary");
//        header("Content-disposition: attachment;filename=".checkReport($_POST['report'],$filename).".rtf");
		
		$filename = checkReport($_POST['report'],$filename);
		header('Content-Disposition: attachment; filename="'.$filename.'.rtf"');
		header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
		header('Content-Length: ' . strlen($convertedRTF));
		header('Connection: close');

        echo($convertedRTF);
//
//        echo  "<script type='text/javascript'>";
//        echo "window.close();";
//        echo "</script>";
    }
}
else
{
	echo "Looks like JobNo is empty";
//    echo  "<script type='text/javascript'>";
//    echo "window.close();";
//    echo "</script>";
}
