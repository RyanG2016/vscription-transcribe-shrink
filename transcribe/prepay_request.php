<?php
require '../api/bootstrap.php';

include_once('data/parts/session_settings.php');
use Src\TableGateways\AccountGateway;


$accID = $_SESSION["accID"];
if(isset($_POST["self"]))
{
    $accID = $_SESSION["userData"]["account"];
}
$currentAccount = \Src\Models\Account::withID($_SESSION["accID"], $dbConnection);
$currentAccount->setCompMins(0);
$currentAccount->save();
    // if($_REQUEST["request"] && $_REQUEST["request"] == "payment"){
    //     initRequest();
    // }
    // function initRequest(){
    //     if($_SESSION["userData"]["promo"] ==2){
    //         echo "success";
    //         $_SESSION["userData"]["promo"] = 1;
    //     }
    // }    
?>