<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Report</title>
    <link href='../data/css/reports.css' type='text/css' rel='stylesheet'/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
    <div class="header"> 
        <div class="head-left">        
            <h2>Billing Report</h2>
        </div>
        <div class="head-right">
           <img src="../data/images/Logo_vScription_Transcribe_Pro.png" alt="vScription Transcribe Pro">
        </div>    
    </div>
<?php

include ("../data/parts/constants.php");
include ("../data/parts/config.php");
include ('../data/thirdparty/phpscripts/html_table.php');

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);


    $sql="SELECT 
		job_id, 
		file_author, 
		file_work_type, 
		file_date_dict, 
		audio_length, 
		file_transcribed_date
    FROM 
		files
	WHERE 
		file_status  = '3' AND 
		isBillable = '1' AND
		billed = '0' AND 
        acc_id = '1' AND
        file_transcribed_date BETWEEN '2020-06-01' AND '2020-06-31'";


if($stmt = mysqli_prepare($con, $sql))
{
	if(mysqli_stmt_execute($stmt)){
			$result = mysqli_stmt_get_result($stmt);

			if(mysqli_num_rows($result) > 0){ 
				$num_rows = mysqli_num_rows($result);
				$htmlhead = $html . "<table class='report'><thead><tr><th class='jobnum'>Job Number</th><th class='author'>Author</th><th class='jobtype'>Job Type</th><th class='datedict'>Date Dictated</th><th class='audiolength'>Audio Length</th><th class='transdate'>Transcribed Date</th></tr></thead><tbody>";

				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$html = $html . "<tr>" . "<td>" . $row['job_id']. "</td>" .
					"<td>" . $row['file_author']. "</td>" .
					"<td>" . $row['file_work_type']. "</td>" .
					"<td>" . $row['file_date_dict']. "</td>" .
					"<td>" . $row['audio_length']. "</td>" .
					"<td>" . $row['file_transcribed_date'] . "</td>" .	
					"</tr>";		

					$minsTotal+=$row['audio_length'];
				}
				// And now the totals:
                //$htmlfoot = "</tbody><tfoot><tr>Total Minutes:". $minsTotal . "</tr></tfoot></table>";
                $htmltablefoot = "</tbody></table>";
                $htmlfoot1 =  "<p>Total Jobs: $num_rows Jobs</p><br/>";
                $htmlfoot2 = "<p>Total Minutes: $minsTotal</p>";
				}
				else {
					echo "No Results Found";
                }
                echo html_entity_decode($htmlhead . $html . $htmltablefoot . $htmlfoot1 . $htmlfoot2);
		}
	}
//$pdf->WriteHTML($html);
//$pdf->Output();
?>
</body>
</html>
