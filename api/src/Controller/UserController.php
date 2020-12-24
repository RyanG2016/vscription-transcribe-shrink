<?php

namespace Src\Controller;

//use PHPMailer\PHPMailer\Exception;
use Src\Models\SR;
use Src\TableGateways\UserGateway;
use Src\TableGateways\accessGateway;
use Src\System\Mailer;

class UserController
{

    private $db;
    private $requestMethod;
    private $userId;
    private $mailer;

    private $accessGateway;
    private $userGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;
        $this->mailer = new Mailer($db);

        $this->userGateway = new UserGateway($db);
        $this->accessGateway = new accessGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId == "available") {
                    $response = $this->getAvailableForWork();
                }
                else if ($this->userId == "sr-enabled") {
                    $response = $this->getSRenabled();
                }else if ($this->userId == "sr-mins") {
                    $response = $this->getSRmins();
                }
                else if ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                }
                break;
            case 'POST':

                if ($this->userId == "set-default") {
                    // set user default access
//                    echo "setting-default-access-for-current-logged-in-user";
                    $response = $this->updateUserDefaultAccess();
                }else if ($this->userId == "set-available") {
                    $response = $this->setAvailableForWork();
                }
                else if ($this->userId == "sr-enabled") {
                    $response = $this->setSRenabled();
                }
                else if ($this->userId == "tutorial-viewed") {
                    $response = $this->tutorialViewed();
                }
                else if($this->userId == null) {
                    $response = $this->createUserFromRequest();
                }else{
                    $response = $this->notFoundResponse();
                }
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->userId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function processPublicRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId == "typists") {
                    $response = $this->getTypistsForCurAdminAccount();
                }
                else if ($this->userId == "available") {
                    $response = $this->getAvailableForWork();
                }
                else if ($this->userId == "sr-enabled") {
                    $response = $this->getSRenabled();
                }
                else if ($this->userId == "sr-mins") {
                    $response = $this->getSRmins();
                }
                else {
//                    $response = $this->getAllUsers();
                    $response = $this->notFoundResponse();
                }
                break;
            case 'POST':

                if ($this->userId == "set-default") {
                    // set user default access
//                    echo "setting-default-access-for-current-logged-in-user";
                    $response = $this->updateUserDefaultAccess();
                }
                else if ($this->userId == "set-available") {
                    $response = $this->setAvailableForWork();
                }
                else if ($this->userId == "sr-enabled") {
                    $response = $this->setSRenabled();
                }
                else if ($this->userId == "tutorial-viewed") {
                    $response = $this->tutorialViewed();
                }
                else if ($this->userId == "invite"){
                    $response = $this->inviteTypistToCurrentAccount();
                }
                else{
                    $response = $this->notFoundResponse();
                }
                break;
//            case 'PUT':
//                $response = $this->updateUserFromRequest($this->userId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteUser($this->userId);
//                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllUsers()
    {
        $result = $this->userGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     * Retrieves typists emails for invitation dropdown for client administrators management screen
     * @return mixed
     */
    private function getTypistsForCurAdminAccount()
    {
        $result = $this->userGateway->getTypists();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     * SET available to work as typist for current logged in user
     * @param int POST av: availability (0,1,2)
     * @return boolean success
     */
    private function setAvailableForWork()
    {
        if(!isset($_POST["av"]) || !is_numeric($_POST["av"]))
        {
            return  false;
        }
        $result = $this->userGateway->setAvailableForWorkAsTypist($_POST["av"]);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }

    /**
     * SET sr_enabled for current user admin account
     * @param int POST sr enabled (0,1)
     */
    private function setSRenabled()
    {
        if(!isset($_POST["sr"]) || !is_numeric($_POST["sr"]))
        {
            return  false;
        }
        if($_SESSION["role"] != 1 && $_SESSION["role"] != 2)
        {
            return false;
        }
        $result = $this->userGateway->setSRforCurrUser($_POST["sr"]);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }

    /**
     * Updates tutorials field in DB to 1 for a page for the current user
     * Updates tutorials session variable
     * @param string POST tutorial page name
     * @return mixed response with header and body
     */
    private function tutorialViewed()
    {
        if(!isset($_POST["page"]) || empty($_POST["page"]))
        {
            return false;
        }
        $result = $this->userGateway->setTutorialViewedForPage($_POST["page"]);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }

    /**
     * Retrieves available to work as typist for current logged in user
     * @return array [body] available
     */
    private function getAvailableForWork()
    {
        $result = $this->userGateway->getAvailableForWorkAsTypist();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }

    /**
     * Retrieves if the current user -> account is sr enabled
     * @return array response [body] sr_enabled <br>
     * int
     * 5 if disabled <br>
     * 1 if enabled
     */
    private function getSRenabled()
    {
        if(!isset($_SESSION['accID']))
        {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = 0;
            return $response;
        }

        $result = $this->userGateway->getSRenabled();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }


    /**
     * Retrieves current user -> account remaining sr minutes
     * @return array response [body] <br> minutes:float
     */
    private function getSRmins()
    {
        if(!isset($_SESSION['accID']))
        {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = 0;
            return $response;
        }

        if($_SESSION["role"] == 1 || $_SESSION["role"] == 2)
        {
            $sr = SR::withAccID($_SESSION["accID"], $this->db);
            $minutes = $sr->getSrMinutesRemaining();

            $result = $minutes;
            if($minutes == null)
            {
                $result = "0.00";
            }

        }else{
//            $result = "Permission denied";
            $result = "00.00";
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }

    private function createUserFromRequest()
    {
        return $this->userGateway->insertNewUser();
    }

    private function updateUserDefaultAccess()
    {
        return $this->userGateway->updateDefaultAccess();
    }

    private function updateUserFromRequest($accID)
    {
        return $this->userGateway->updateUser($accID);
    }

    private function getUser($id)
    {
        $result = $this->userGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    /*private function formatUserResult($userName, $status, $error)
    {
        return array(
            "user_name" => $userName,
            "status" => $status,
            "error" => $error
        );
    }*/

    private function inviteTypistToCurrentAccount()
    {


        if(!isset($_POST["email"]) || empty($_POST["email"]) ||
            !isset($_SESSION['role']) || $_SESSION['role'] != 2
        ) {
            return generateApiHeaderResponse("Invalid Input (UC-I1)", true);
        }

        $user = $this->userGateway->getUserByEmail($_POST["email"]);
        if($user)
        {
//            if($user["email_notification"] != 1) // oldTodo OR plan ID != 3
            if($user["typist"] != 1)
            {
                return generateApiHeaderResponse("User is not accepting invites at the moment.", true);
            }
        }else{
            // send signup and accept invite email to user email address
            // 1. send signup_typist_invitation email
            $this->mailer->sendEmail(7, $_POST["email"], $_SESSION["acc_name"], $_SESSION["accID"]);
            return generateApiHeaderResponse("Signup invitation sent, user will be granted permission once signed up.", false);
//            return generateApiHeaderResponse("User not found.", true);
        }

        $accessID = $this->accessGateway->internalManualInsertAccessRecord($_SESSION["accID"], $user["id"], $_POST["email"], 6);
        if(!$accessID){
            return generateApiHeaderResponse("Failed to send invitation. (AID-1)", true);
        }
        $emailSent = $this->mailer->sendEmail(6, $_POST["email"], $_SESSION["acc_name"], $accessID);
        if( $emailSent )
        {
            return generateApiHeaderResponse("Invitation sent.", false);
        }else{
            return generateApiHeaderResponse("Failed to send invitation.", true);
        }

    }


    private function deleteUser($id)
    {
        if ($id == null) {
            return $this->notFoundResponse();
        }
        $result = $this->userGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->userGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => 'User Deleted.'
        ]);
        return $response;
    }

    /*private function validateUser($input)
    {
        if (!isset($input['firstname'])) {
            return false;
        }
        if (!isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function validateAndReturnDate($date)
    {
        // (accepted format: yyyy-mm-dd)
        $dateArr = explode("-", $date);
        if (sizeof($dateArr) == 3 && checkdate($dateArr[1], $dateArr[2], $dateArr[0])) {
            return $dateArr[0] . "-" . $dateArr[1] . "-" . $dateArr[2];
        } else {
            return false;
        }

    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);
        return $response;
    }*/
/*
    private function errorOccurredResponse($error_msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Error Occurred';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Error Occurred ' . $error_msg
        ]);
        return $response;
    }*/

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'User Not Found'
        ]);
        return $response;
    }
}
