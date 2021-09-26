<?php
//session_start(['cookie_lifetime' => 86400,'cookie_secure' => true,'cookie_httponly' => true]);
//require __DIR__ . "/../api/bootstrap.php";
date_default_timezone_set('America/Winnipeg');
include('data/parts/session_settings.php');
include('testclass.php');
//include('data/parts/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .testdata td{
            font-size: 16px;
        }
        .testdata td:first-child{
            font-size: 16px;
            background-color: #00004d;
            font-weight: bold;
            width: 250px;
        }
    </style>
</head>
<body style="background-color: black; color: white; font-size: 15px;">

<?php
//$ch = curl_init('https://apitest.authorize.net/xml/v1/request.api');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_VERBOSE, true);
//$data = curl_exec($ch);
//curl_close($ch);
//echo $data;
//$testInt = new testInt();

// CHECK TLS VERSION
    //$ch = curl_init('https://www.howsmyssl.com/a/check');
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //$data = curl_exec($ch);
    //curl_close($ch);
    //
    //$json = json_decode($data);
    //echo "Connection uses " . $json->tls_version ."\n";
//echo $filename = uniqid(time()."_", true) . '.pdf';
//phpinfo();
$url = "https://pro.vtex/";
//$headers = get_headers($url, 1);
$key = ini_get("session.upload_progress.prefix") . "jobUpload";
$sub = $_SERVER['REQUEST_TIME']-strtotime($_SESSION['lastPing']);
echo "<table class='testdata'>
     <tr>
         <td>
            Current Path
         </td>
         <td>
            ".pathinfo(basename($_SERVER['PHP_SELF']), PATHINFO_FILENAME)."
         </td>
     </tr>
     <tr>
         <td>
            Session save DIR 
         </td>
         <td>
            ".session_save_path()."
         </td>
     </tr>
     <tr>
         <td>
            Session name 
         </td>
         <td>
            ".ini_get("session.name")."
         </td>
     </tr>
     <tr>
         <td>
            MySQL format  Y-m-d H:i:s
         </td>
         <td>
            ".date("Y-m-d H:i:s")."
         </td>
     </tr>
     <tr>
         <td>
            MySQL format usage in calendar
         </td>
         <td>
            ".(date("Y-m").'-01')."
         </td>
     </tr>
     <tr>
         <td>
            Server request time
         </td>
         <td>
            ".
        date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']). " | ".  $_SERVER['REQUEST_TIME']
    ."
         </td>
     </tr>
     <tr>
         <td>
            Last ping
         </td>
         <td>
            ".
                $_SESSION['lastPing'] . " | " . strtotime($_SESSION['lastPing'])
            ."
         </td>
     </tr>
     <tr bgcolor='#006400'>
         <td>
            Ping Diff
         </td>
         <td>
            ".
//                $sub . " sec | " . date("H:i:s > e", $sub)
                $sub . " sec | " . date("H:i:s > e")
            ."
         </td>
     </tr>
     <tr>
         <td>
            Timezone
         </td>
         <td>
            ".
                date_default_timezone_get()
            ."
         </td>
     </tr>
     <tr>
         <td>
            Prefix
         </td>
         <td>
            ".
                ini_get("session.upload_progress.prefix")
            ."
         </td>
     </tr>
     <tr>
         <td>
            Progress Name
         </td>
         <td>
            ".
                ini_get("session.upload_progress.name")
            ."
         </td>
     </tr>
     <tr>
         <td>
            Progress Enabled
         </td>
         <td>
            ".
    ini_get("session.upload_progress.enabled")
            ."
         </td>
     </tr>
     <tr>
         <td>
            session.auto_start
         </td>
         <td>
            ".
    ini_get("session.auto_start")
            ."
         </td>
     </tr>
     <tr>
         <td>
            session.upload_progress.cleanup
         </td>
         <td>
            ".
    ini_get("session.upload_progress.cleanup")
            ."
         </td>
     </tr>
     <tr>
         <td>
            output buffering
         </td>
         <td>
            ".
    ob_get_level()
            ."
         </td>
     </tr>
     <tr>
         <td>
            CLI type
         </td>
         <td>
            ".
    php_sapi_name()
            ."
         </td>
     </tr>
     <tr>
         <td>
            sessions path
         </td>
         <td>
            ".ini_get("session.save_path")
            ."
         </td>
     </tr>
 </table>";

//echo ("<pre>".print_r($headers,true)."</pre>");

echo "</br>";
echo "<hr>";
//print_r(curl_version());
//echo '<br/>';
//echo '<br/>';

//echo '<br/>';
//echo '<br/>';
//echo md5(time() . mt_rand(1,1000000));
//$captions = "[{\"start\":0,\"end\":5,\"lines\":[\"And then when I picked police service that we might clearly define between the\"]},{\"start\":6.36,\"end\":11.19,\"lines\":[\"two parties, exactly what it is each group looks to get out of,\"]},{\"start\":11.43,\"end\":12.263,\"lines\":[\"uh,\"]},{\"start\":12.51,\"end\":17.51,\"lines\":[\"out of this relationship and out of these officers being in schools.\"]},{\"start\":18.28,\"end\":23.19,\"lines\":[\"Uh, I appreciate the two reports that we already have on this.\"]},{\"start\":23.91,\"end\":24.743,\"lines\":[\"Um,\"]},{\"start\":25.23,\"end\":30.09,\"lines\":[\"are these reports that we made public in the past or are available on our\"]},{\"start\":30.09,\"end\":30.923000000000002,\"lines\":[\"website?\"]},{\"start\":35.7,\"end\":38.99,\"lines\":[\"I don't know if they're on a website or not just you and Brenda, do you know?\"]},{\"start\":45.44,\"end\":47.87,\"lines\":[\"Well, we'll, we'll follow up. Like, yeah,\"]},{\"start\":49.88,\"end\":53.09,\"lines\":[\"there might be some value in, um,\"]},{\"start\":53.45,\"end\":58.45,\"lines\":[\"initiating some kind of preliminary discussions with some community leaders,\"]},{\"start\":59.72,\"end\":64.37,\"lines\":[\"um, newcomer, community leaders and organizations. Um,\"]},{\"start\":65.09,\"end\":69.77,\"lines\":[\"and, and see if there is any specifics, um,\"]},{\"start\":70.46,\"end\":74.39,\"lines\":[\"like there's a defund, the police movement. Um,\"]},{\"start\":75.11,\"end\":79.55,\"lines\":[\"but what kind of clarifying some of the feedback,\"]},{\"start\":79.58,\"end\":83.6,\"lines\":[\"because if there's a way that we can improve the program,\"]},{\"start\":84.77,\"end\":89.77,\"lines\":[\"I also want to be capturing some of that information that isn't necessarily just\"]},{\"start\":90.17,\"end\":93.56,\"lines\":[\"strictly about cutting the program. It's,\"]},{\"start\":93.65,\"end\":97.7,\"lines\":[\"it's about clearly defining our objectives and seeing if,\"]},{\"start\":98.03,\"end\":100.06,\"lines\":[\"if we're meeting those. And I,\"]},{\"start\":100.06,\"end\":103.55,\"lines\":[\"and I think part of that is the history that this began in the community.\"]},{\"start\":103.94,\"end\":108.17,\"lines\":[\"This was the no send renewal corporation, as Michael said at the last meeting.\"]},{\"start\":108.44,\"end\":110.69,\"lines\":[\"And they, and so I think there's,\"]},{\"start\":110.75,\"end\":114.59,\"lines\":[\"there's some background information that we can certainly, uh, put up,\"]},{\"start\":114.89,\"end\":118.79,\"lines\":[\"but our hope was that we would do completely different form of assessment than\"]},{\"start\":118.79,\"end\":123.65,\"lines\":[\"these, these are very outdated now, and this would not be how we would proceed,\"]},{\"start\":123.68,\"end\":128.54,\"lines\":[\"but I appreciate the comments that that just helps reinforce it. Okay.\"]},{\"start\":128.54,\"end\":131.81,\"lines\":[\"Thank you. Uh, I will open it up to, uh,\"]},{\"start\":132.14,\"end\":135.62,\"lines\":[\"her two other trustees that have joined us. Uh, again,\"]},{\"start\":135.62,\"end\":140.54,\"lines\":[\"I'll limit you to two minutes just so that we can get onto our last agenda item\"]},{\"start\":140.54,\"end\":142.79,\"lines\":[\"before six 30. Um,\"]},{\"start\":142.79,\"end\":146.18,\"lines\":[\"so over with trustee Schakowsky and then trustee Chen.\"]},{\"start\":152.63,\"end\":156.35,\"lines\":[\"Yes. Um, I would like to hear the voices of the students,\"]},{\"start\":156.68,\"end\":160.1,\"lines\":[\"the staff administration, and the parents.\"]},{\"start\":160.43,\"end\":165.43,\"lines\":[\"I think then we would get the full circle of how they're all feeling,\"]},{\"start\":165.83,\"end\":167.42,\"lines\":[\"especially in the high schools.\"]},{\"start\":167.84,\"end\":172.84,\"lines\":[\"I'm hearing some community and staff that they treasure this,\"]},{\"start\":173.81,\"end\":178.19,\"lines\":[\"um, with having place in our school. And as a parent,\"]},{\"start\":178.46,\"end\":182.44,\"lines\":[\"I have been able to say it has come in handy.\"]},{\"start\":187.86,\"end\":189.21,\"lines\":[\"Excellent. Thank you, trustee Chen.\"]},{\"start\":192.66,\"end\":197.19,\"lines\":[\"Uh, yeah, I'm kind of, uh, in between the two,\"]},{\"start\":197.28,\"end\":198.81,\"lines\":[\"uh, thoughts in here,\"]},{\"start\":199.53,\"end\":204.53,\"lines\":[\"one is about doing another round of evaluation and I see the value is because,\"]},{\"start\":206.82,\"end\":207.653,\"lines\":[\"uh,\"]},{\"start\":207.78,\"end\":212.46,\"lines\":[\"this report was conducted in 2013 and 2014.\"]},{\"start\":212.52,\"end\":214.2,\"lines\":[\"And the demographic of,\"]},{\"start\":214.65,\"end\":219.54,\"lines\":[\"especially the immigrants and refugees students have changed dramatically,\"]},{\"start\":219.54,\"end\":221.43,\"lines\":[\"especially in recent years. Uh,\"]},{\"start\":221.55,\"end\":225.33,\"lines\":[\"there are a lot of refugee students coming from war torn countries,\"]},{\"start\":225.39,\"end\":229.02,\"lines\":[\"such as Sue and courteous Shanda Yazidi. Um,\"]},{\"start\":229.08,\"end\":233.85,\"lines\":[\"so I would assume that that is Sergeant report, um,\"]},{\"start\":234.63,\"end\":235.8,\"lines\":[\"or not, um,\"]},{\"start\":236.16,\"end\":241.16,\"lines\":[\"didn't have much voice from those communities and another evaluation may,\"]},{\"start\":241.92,\"end\":246.45,\"lines\":[\"uh, include, uh, some of their voices. So I see the value of, uh,\"]},{\"start\":246.45,\"end\":250.5,\"lines\":[\"not another evaluation. Um, but on the other hand, um,\"]},{\"start\":251.37,\"end\":256.26,\"lines\":[\"also, uh, from what we heard, uh, from the, uh, newcomer communities and,\"]},{\"start\":256.59,\"end\":259.05,\"lines\":[\"and that, um,\"]},{\"start\":259.17,\"end\":262.17,\"lines\":[\"students or parents from those countries may not,\"]},{\"start\":262.56,\"end\":264.63,\"lines\":[\"they hesitate to speak up, uh,\"]},{\"start\":264.63,\"end\":268.83,\"lines\":[\"especially police to seen as authority in many of those countries since\"]},{\"start\":268.92,\"end\":273.48,\"lines\":[\"associated with associated with negative experiences. Um,\"]},{\"start\":273.72,\"end\":278.31,\"lines\":[\"and, uh, my, my, um, point, um,\"]},{\"start\":278.46,\"end\":281.07,\"lines\":[\"also is about, um,\"]},{\"start\":281.49,\"end\":285.81,\"lines\":[\"the financial challenge that we may face in the coming year.\"]},{\"start\":286.35,\"end\":288.72,\"lines\":[\"Um, I think that this is, uh,\"]},{\"start\":288.84,\"end\":292.44,\"lines\":[\"this is the opportunity for us to reevaluate. Um,\"]},{\"start\":293.19,\"end\":296.82,\"lines\":[\"I was spending priorities in the past years with, um,\"]},{\"start\":297.42,\"end\":300.21,\"lines\":[\"doing the budget time where we've always said that we need to look at what\"]},{\"start\":300.21,\"end\":304.95,\"lines\":[\"programs we want to keep and what programs so he, yeah, so, um,\"]},{\"start\":304.98,\"end\":309.27,\"lines\":[\"I think I see this as an opportunity, uh, and plus we heard, uh,\"]},{\"start\":309.3,\"end\":313.47,\"lines\":[\"voices from the community, um, and I think sometimes, uh,\"]},{\"start\":313.95,\"end\":316.62,\"lines\":[\"one way here that, um,\"]},{\"start\":316.92,\"end\":320.64,\"lines\":[\"people value the program and sometimes it's about the equally look at the\"]},{\"start\":320.64,\"end\":324.54,\"lines\":[\"alternatives or if we present alternatives to, uh,\"]},{\"start\":325.66,\"end\":329.94,\"lines\":[\"to the parents, students, staff, and community.\"]},{\"start\":330.36,\"end\":333.03,\"lines\":[\"Um, so yeah,\"]},{\"start\":333.36,\"end\":338.19,\"lines\":[\"I'm kind of a thing between two options. Um, I see both,\"]},{\"start\":338.28,\"end\":342.33,\"lines\":[\"I see value as in both. Um, yeah, but just my,\"]},{\"start\":342.42,\"end\":346.89,\"lines\":[\"just my thoughts on, yeah, thank you. Uh, it does. Thank you,\"]},{\"start\":346.89,\"end\":349.05,\"lines\":[\"trustee chin. Uh, okay.\"]},{\"start\":349.05,\"end\":353.76,\"lines\":[\"So I think the administration and the committee kind of have a strong\"]},{\"start\":353.76,\"end\":357.62,\"lines\":[\"understanding of where we're going and, and, um,\"]},{\"start\":358.79,\"end\":359.623,\"lines\":[\"<br>>> Speaker 1: And\"]},{\"start\":359.68,\"end\":363.67,\"lines\":[\"<br>>> Speaker 0: What we've said today just reinforces where the administration is. So,\"]},{\"start\":364.33,\"end\":366.85,\"lines\":[\"<br>>> Speaker 1: Um, uh, I will, yeah,\"]},{\"start\":366.88,\"end\":371.62,\"lines\":[\"<br>>> Speaker 0: Move on then to the next item, which is the WTA request for funding.\"]},{\"start\":372.36,\"end\":376.45,\"lines\":[\"And I believe this came out of our WTA, um,\"]},{\"start\":377.35,\"end\":380.32,\"lines\":[\"um, WSD joint committee meeting.\"]}]";

//echo ("<pre>".print_r(json_decode($captions, true),true)."</pre>");
//print_r($_SESSION, true);
echo ("<pre>".print_r($_SESSION,true)."</pre>");
	echo '<br/>';
	echo '<br/>';

//    $key = ini_get("session.upload_progress.prefix") . $_POST[ini_get("session.upload_progress.name")];

    echo "php info: " . phpinfo();
//    echo "</br>";
//    echo "</br>";
//    echo $key;
//    var_dump($_SESSION[$key]);
/*    echo json_encode($_SESSION["upload_progress_job_upload"]);
    echo "</br>";
    echo "</br>";*/

?>

</body>
</html>
