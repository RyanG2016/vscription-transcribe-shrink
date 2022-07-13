<?php

namespace Src\TableGateways;

use PDOException;
use Src\Enums\FILE_STATUS;
use Src\Enums\USER_ACCOUNT_STATUS;
use Src\Models\BaseModel;
use Src\Models\User;
use Src\TableGateways\logger;
use Src\System\Mailer;

require "filters/usersFilter.php";
include_once "common.php";

class UserGateway implements GatewayInterface
{

    private $db;
    private $logger;
    private $API_NAME;
    private $mailer;

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
        $this->API_NAME = "Users";
        $this->mailer = new Mailer($db);
    }

    public function findAll()
    {
        $filter = parseUserParams(true);

        $statement = "
            SELECT 
                   users.id,
                    users.first_name,
                    users.last_name,
                    users.email,                 
                    users.country,
                    users.city,
                    users.state,
                    users.zipcode,
                    users.address,
                    users.registeration_date,
                    users.last_ip_address,
                    users.account_status,
                    users.last_login,
                    users.newsletter,
                    users.shortcuts,
                    users.dictionary,
                    users.email_notification,
                    users.account,
                    users.enabled,
                    users.def_access_id,
                    accounts.acc_name,
                    access.acc_role
                                      
            FROM
                users 
            LEFT JOIN access ON users.def_access_id = access.access_id
            LEFT JOIN accounts ON access.acc_id = accounts.acc_id
        " . $filter . ";";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    //            "draw"            => intval( $_REQUEST['draw'] ),
                    //            "recordsTotal"    => intval( 2 ),
                    //            "recordsFiltered" => intval( 1 ),
                    "data" => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }


    /**
     * Retrieves typists emails for invitation dropdown for client administrators management screen
     * @return mixed
     */
    public function getTypists()
    {
        /*$statement = "
            SELECT 
                   users.id,
                    users.email,
                    users.plan_id,
                    users.account_status,
                    users.email_notification,
                    accounts.acc_name as 'admin_of',
                    access.acc_role                                      
            FROM
                users
            LEFT JOIN access ON users.id = access.uid
            LEFT JOIN accounts ON users.account = accounts.acc_id
        where users.enabled = 1 
                and account_status = 1
                and access.acc_role != 3

        */

        $statement = "
            select users.id, email
            from users
            where users.account_status = 1 and users.enabled = 1 and users.typist = 1 and
                (
                    select count(access.acc_id) from access where access.acc_id = ? and uid = users.id and (acc_role = 3 OR acc_role = 6)
                ) != 1
            group by users.id order by users.id";
        //group by users.email
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    //            "draw"            => intval( $_REQUEST['draw'] ),
                    //            "recordsTotal"    => intval( 2 ),
                    //            "recordsFiltered" => intval( 1 ),
                    "data" => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Retrieves typists emails for invitation dropdown for client administrators management screen
     * Also for typist billing
     * @return mixed
     */
    public function getAllTypists()
    {
        /*$statement = "
            select id,
                   first_name, 
                   last_name, 
                   email,
                   concat(first_name, ' ', last_name) as 'name'
            from users 
            where typist != 0 and account_status = 1;        
        ";*/
        $statement = "
            select
                id, count(f.job_transcribed_by) as 'all_time_jobs',
                   count(case when f.typ_billed = 0 then 1 end) as 'unbilled',
                first_name, last_name, email, concat(first_name, ' ', last_name) as 'name'
            from users
            left join
                 files f
                 on users.email = f.job_transcribed_by
            
            
            where
                f.job_transcribed_by is not null
              and typist != 0
              and f.file_status in (".FILE_STATUS::COMPLETED.")
              and f.isBillable = 1
              and account_status = 1
            
            group by job_transcribed_by;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    "count"    => $statement->rowCount(),
                    "data" => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }
    }

        /**
     * Retrieves a single typist email to be assigned to a new account
     * Returns typist UID
     * @return mixed
     */
    public function getRandomTypist()
    {
        $statement = "
        SELECT id,email,first_name,last_name
        FROM users
        WHERE typist = 1 
                AND enabled = 1 
                AND account_status = 1
                AND email_notification = 1
                AND last_login >= DATE(NOW()) - INTERVAL 5 DAY
                ORDER BY RAND()
                LIMIT 1;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    "count"    => $statement->rowCount(),
                    "data" => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            // The code below works but it is easier to randomize the query results and return one record;
            // if ($result['count'] > 1) {
            //     $winner = $result['data'][mt_rand(0, count($result['data'])-1)]['email'];
            // }
            return $result[0]['id'];
        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }
    }


    /**
     * [Mail] [Mailing List] Retrieves current logged in Client Account's typists emails for mailing list for job updates
     * @return mixed
     */
    public function getCurrentTypistsForJobUpdates()
    {

        $statement = "select u.email
                        FROM access
                    INNER JOIN users u on access.uid = u.id
                    where acc_id = ? and acc_role = 3 and email_notification = 1";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * [Mail] [Mailing List] Retrieves Account Admin Email to inform of job completion
     * @return mixed
     */
    public function getClientAccAdminsEmailForJobUpdates()
    {

        $statement = "select u.email
            from access
            INNER JOIN users u on access.uid = u.id
            where acc_id = ? and acc_role = 2 and email_notification = 1";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {

        $statement = "
            SELECT 
                   users.id,
                    users.first_name,
                    users.last_name,
                    users.email,
                    users.country,
                    users.city,
                    users.state,
                    users.zipcode,
                    users.address,
                    users.registeration_date,
                    users.last_ip_address,
                    users.account_status,
                    users.last_login,
                    users.newsletter,
                    users.shortcuts,
                    users.dictionary,
                    users.email_notification,
                    users.account,
                    users.enabled,
                    users.def_access_id
                   
                                      
            FROM
                users
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    /**
     * Retrieves available to work as typist for current logged in user
     * @return int typist status (0, 1, 2)
     */
    public function getAvailableForWorkAsTypist()
    {

        $statement = "
            SELECT 
                   users.typist       
            FROM
                users
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"]));
            $result = $statement->fetch();
            if($statement->rowCount() > 0)
            {
                return $result["typist"]==0?5:$result["typist"];
            }
            return false;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    /**
     * Retrieves user custom transcribe expandable shortcuts
     * @return string|array json shortcuts
     */
    public function getUserShortcuts()
    {

        $statement = "
            SELECT 
                   users.shortcuts       
            FROM
                users
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"]));
            $result = $statement->fetch();
            if($statement->rowCount() > 0)
            {
                if(isset($_GET['dt'])){
                    return '{ "data": '. $result["shortcuts"] .' }';
                }
                return $result["shortcuts"];
            }
            return "{}";
        } catch (\PDOException $e) {
            return "{}";
//            exit($e->getMessage());
        }
    }

        /**
     * Retrieves user custom transcribe expandable shortcuts
     * @return string|array json shortcuts
     */
    public function getUserDefaultCompactView()
    {

        $statement = "
            SELECT 
                   users.default_compact_view       
            FROM
                users
            WHERE
                users.id = ?";

            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array($_SESSION["uid"]));
                $result = $statement->fetch();
                if($statement->rowCount() > 0)
                {
                    return $result["default_compact_view"]==0?5:$result["default_compact_view"]; //If we return a 0, the ajax treats the response as an error which is why we return 5
                }
                return false;
            } catch (\PDOException) {
                return false;
            }
    }

    /**
     * Retrieves if the current user -> account is sr enabled
     * @return int
     * 5 if disabled <br>
     * 1 if enabled
     */
    public function getSRenabled($accID)
    {

        $statement = "
            SELECT 
                   sr_enabled       
            FROM
                accounts
            WHERE
                acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accID));
            $result = $statement->fetch();
            if($statement->rowCount() > 0)
            {
                return $result["sr_enabled"]==0?5:$result["sr_enabled"];
            }
            return false;
        } catch (\PDOException) {
            return false;
        }
    }

        /**
     * Retrieves if the current user -> auto job list refresh is enabled
     * @return int
     * 0 if disabled <br>
     * 1 if enabled
     */
    public function getListRefreshEnabled($accID)
    {

        $statement = "
            SELECT 
                   auto_list_refresh       
            FROM
                accounts
            WHERE
                acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accID));
            $result = $statement->fetch();
            if($statement->rowCount() > 0)
            {
                return $result["auto_list_refresh"];
            }
            return false;
        } catch (\PDOException) {
            return false;
        }
    }

            /**
     * Retrieves auto job list refresh interval
     * @return int
     * 30 if value set to less than 30 <br>
     */
    public function getListRefreshInterval($accID)
    {

        $statement = "
            SELECT 
                   auto_list_refresh_interval       
            FROM
                accounts
            WHERE
                acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accID));
            $result = $statement->fetch();
            if($statement->rowCount() > 0)
            {
                return $result["auto_list_refresh_interval"];
            }
            return false;
        } catch (\PDOException) {
            return false;
        }
    }

       /**
     * SET auto list refresh enable for current user logged in account
     * @param $auto_list_refresh_enabled (0,1)
     * @return boolean success
     */
    public function setAutoListRefresh($auto_list_refresh_enabled, $accID)
    {

        $statement = "
            UPDATE
                accounts
                   SET       
                   auto_list_refresh = ?
            WHERE
                acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($auto_list_refresh_enabled, $accID));
            return $statement->rowCount();
        } catch (\PDOException) {
            return false;
        }
    }

           /**
     * SET auto list refresh interval for current user logged in account
     * @param $auto_list_refresh_intervak (0,1)
     * @return boolean success
     */
    public function setAutoListRefreshInterval($auto_list_refresh_interval, $accID)
    {

        $statement = "
            UPDATE
                accounts
                   SET       
                   auto_list_refresh_interval = ?
            WHERE
                acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($auto_list_refresh_interval, $accID));
            return $statement->rowCount();
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * SETs available to work as typist for current logged in user
     * @param $availability (0,1,2)
     * @return boolean success
     */
    public function setAvailableForWorkAsTypist($availability)
    {

        $statement = "
            UPDATE
                users
                   SET       
                typist = ?
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($availability, $_SESSION["uid"]));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

        /**
     * SETs default compact view mode for current logged in user
     * @param $availability (0,1,2)
     * @return boolean success
     */
    public function setDefaultCompactView($state)
    {

        $statement = "
            UPDATE
                users
                   SET       
                default_compact_view = ?
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($state, $_SESSION["uid"]));
            $_SESSION["defaultCompactView"] = $state; //Update the session variable
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    /**
     * SET sr_enabled for current user logged in account
     * @param $sr_enabled (0,1)
     * @return boolean success
     */
    public function setSRforCurrUser($sr_enabled, $accID)
    {

        $statement = "
            UPDATE
                accounts
                   SET       
                sr_enabled = ?
            WHERE
                acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($sr_enabled, $accID));
            return $statement->rowCount();
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Updates tutorials field in DB to 1 for a page for the current user
     * Updates tutorials session variable
     * @param $page string page name
     * @return boolean success
     */
    public function setTutorialViewedForPage($page)
    {
        $tutorialsStr = isset($_SESSION["tutorials"])?$_SESSION["tutorials"]:"{}";
        $tutorialsJson = json_decode($tutorialsStr, true);
        $tutorialsJson[$page] = 1; // set as viewed
        $updatedJsonStr = json_encode($tutorialsJson);

        $statement = "
            UPDATE
                users
                   SET       
                tutorials = ?
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($updatedJsonStr, $_SESSION["uid"]));
            if($statement->rowCount())
            {
                $_SESSION["tutorials"] = $updatedJsonStr; // update/refresh session variable with new data
                return true;
            }
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
        return false;
    }

    /**
     * retrieves user data
     * @param $email string user email address
     * @return string JSON of user object
     */
    public function getUserByEmail($email)
    {

        $statement = "
            SELECT 
                   users.id,
                    users.first_name,
                    users.last_name,
                    users.email,
                    users.country,
                    users.city,
                    users.state,
                    users.zipcode,
                    users.address,
                    users.registeration_date,
                    users.last_ip_address,
                    users.account_status,
                    users.last_login,
                    users.newsletter,
                    users.shortcuts,
                    users.dictionary,
                    users.email_notification,
                    users.account,
                    users.enabled,
                    users.typist                                      
            FROM
                users
            WHERE
                users.email = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($email));
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $statement->fetch();
//            return $result;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    public function insertNewUser()
    {
        // Required Fields
        if (
            !isset($_POST["first_name"]) ||
            !isset($_POST["last_name"]) ||
            !isset($_POST["email"]) ||
            !isset($_POST["country"]) ||
            !isset($_POST["newsletter"]) ||
            !isset($_POST["enabled"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (1)");
        }

        // Sql Injection Check
        if(!sqlInjectionCreateCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (505)");
        }

        // Parse post request params/fields

        $fields = "";
        $valsQMarks = "";
        $valsArray = array();
        $i = 0;
        $len = count($_POST);

        foreach ($_POST as $key => $value) {

            // setting all empty params to ''
            // skip empty values
            if (empty($value)) {
//                $input = "";
                continue;
            }


            $fields .= "`$key`";

            array_push($valsArray, $value);
            $valsQMarks .= "?";

            $fields .= ", ";
            $valsQMarks .= ", ";

            $i++;
        }

        // Optional Fields Calculations //
        // account_status
        $fields .= "`account_status`";
        $valsQMarks .= "?";
        array_push($valsArray, 5);

        // password
        $fields .= ", " . "`password`";
        $valsQMarks .= ", ?";

        $newPass = $this->getNewPasswordWithHash();
        array_push($valsArray, $newPass["hash"]);

        // ip address
        $fields .= ", " . "`last_ip_address`";
        $valsQMarks .= ", ?";
        array_push($valsArray, getIP());



        // insert to DB //
        $statement = "INSERT
                        INTO 
                            users 
                            (
                             " . $fields . "
                             ) 
                         VALUES 
                                (" . $valsQMarks . ")";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "Created User: " . $_POST["email"]);
                $this->mailer->sendEmail(16, $_POST["email"], "", $newPass["pwd"]);
                return $this->oKResponse($this->db->lastInsertId(), "User Created");
            } else {
//                return $this->errorOccurredResponse("Couldn't Create User");
                if(strpos($statement->errorInfo()[2], 'Duplicate entry') !== false){
                    return $this->errorOccurredResponse("User Already Exists");
                }else{
                    return $this->errorOccurredResponse("Couldn't Create User");
//                    return $this->errorOccurredResponse("Couldn't Create User" . print_r($statement->errorInfo()));
                }

            }

        } catch (PDOException $e) {
//            die($e->getMessage());
//            return false;
            return $this->errorOccurredResponse("Couldn't Create User (2)");
        }
    }


    /**
     * Updates `account` field in `users` tbl with the user made client admin account ID
     * and sets session variables with the account data
     * @internal
     * @param $accID int client admin account ID made by the user
     * @param $accName string client admin account name
     * @return boolean true -> success | false -> failed to update
     */
    public function internalUpdateUserClientAdminAccount($accID, $accName) {

        // update user access //
        $statement = "UPDATE
                            users
                        SET  
                            account = ?
                        WHERE
                            id = ?
                        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $accID,
                $_SESSION['uid']
            ));

            if ($statement->rowCount() > 0) {

                // setting session variables to bypass the need of logging out and in again
                $_SESSION["adminAccount"] = $accID;
                $_SESSION["adminAccRetTime"] = 14;
                $_SESSION["adminAccLogRetTime"] = 90;
                $_SESSION["adminAccountName"] = $accName;

            return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    /**
     * Clears def_access_id to null
     * used if a revoke access is invoked on a access entry which is the default for a user (will fail due to foreign key check)
     * @internal
     * @param $uid int user id
     * @return boolean true -> success | false -> failed to update
     */
    public function internalUpdateUserClearDefaultAccess($uid) {

        // update user access //
        $statement = "UPDATE
                            users
                        SET  
                            def_access_id = null
                        WHERE
                            id = ?
                        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $uid
            ));

            return $statement->rowCount();

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function updateDefaultAccess()
    {
        // Required Fields
        if (
            !isset($_POST["acc_id"]) ||
            !isset($_POST["acc_role"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (1)");
        }

        // Sql Injection Check
        if(!sqlInjectionUpdateDefAccessCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (505-UPDEF)");
        }

        $uid = $_SESSION['uid'];

        // allow admin to set default access to a certain user instead of self
        if(isset($_POST["uid"]) && is_numeric($_POST["uid"])) {
            if($_SESSION["role"] == 1) {
                $uid = $_POST["uid"];
            }
        }

        // insert to DB //
        $statement = "SELECT
            access_id
        FROM
            access
        WHERE
            acc_id = ?
        AND
            acc_role = ?
        AND
              uid = ?
        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute( array(
                $_POST["acc_id"],
                $_POST["acc_role"],
                $uid
            ) );

            if ($statement->rowCount() > 0) {
                $result = $statement->fetch();
                return $this->setDefaultAccess($result['access_id'], $uid);
            } else {
                return $this->errorOccurredResponse("You don't have permission for this access token.");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't retrieve access token information.");
        }
    }

    // this is executed after updateDefaultAccess checks if the role & acc IDS match for the current logged in user
    public function setDefaultAccess($defAccessID, $uid) {

        // update user access //
        $statement = "UPDATE
                            users
                        SET  
                            def_access_id = ?
                        WHERE
                            id = ?
                        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($defAccessID, $uid));

//            if ($statement->rowCount() > 0) {
                return $this->oKResponse(0, "Default Access set successfully");
//            } else {
//                return $this->errorOccurredResponse("Couldn't set default");
//                return $this->errorOccurredResponse("Couldn't set default" .
//                    print_r($statement->errorInfo())
//                );
//            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't set default (2)");
        }
    }


    public function updateUser($id)
    {
        parse_str(file_get_contents('php://input'), $put);

        // Required Fields
        /*if (
            !isset($put["first_name"]) ||
            !isset($put["last_name"]) ||
            !isset($put["email"]) ||
            !isset($put["country_id"]) ||
            !isset($put["newsletter"]) ||
            !isset($put["enabled"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (31)");
        }*/

        // Sql Injection Check
        if(!sqlInjectionUserCheckPassed($put))
        {
            return $this->errorOccurredResponse("Invalid Input (3505)");
        }

        // Parse post request params/fields
        $valPairs = "";
        $valsArray = array();

        $i = 0;
        $len = count($put);

        foreach ($put as $key => $value) {

            // setting all empty params to 0
            if (empty($value)) {
                $value = 0;
            }

            $valPairs .= "`$key` = ";
            array_push($valsArray, $value);
            $valPairs .= "?";

            if ($i != $len - 1) {
//                 not last item add comma
                $valPairs .= ", ";
            }

            $i++;
        }

        array_push($valsArray, $id);


        // update DB //
        $statement = "UPDATE
                        users 
                        SET 
                             " . $valPairs . " 
                        WHERE 
                            id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "Updated User: " . $id);
                return $this->oKResponse($id, "User Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update user or no changes were found to update");
//                return $this->errorOccurredResponse("Debug " . print_r($statement->errorInfo()) . "\n <br>"
//                . $statement->queryString);
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    
    public function updateCurrentUser()
    {
        $id = $_SESSION["uid"];

        // required fields
        if (
            !isset($_POST["first_name"]) ||
            !isset($_POST["last_name"]) ||
            !isset($_POST["email"]) ||
            !isset($_POST["country"]) ||
            !isset($_POST["newsletter"]) ||
            !isset($_POST["email_notification"]) ||
            !isset($_POST["city"]) ||
            !isset($_POST["state"]) ||
            !isset($_POST["address"]) ||
            !isset($_POST["zip"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (VSPT-U400)");
        }
        // var_dump($_POST);
        // validation
        foreach ($_POST as $keyPost => $valuePost) {
            switch ($keyPost)
            {
                case 'first_name':
                case 'last_name':
                    if(!preg_match("/^[a-z '\-]{2,50}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U201)");
                    }
                    break;

                case 'address':
                    if(!preg_match("/^[a-z0-9_\- .]{0,100}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U202)");
                    }
                    break;

                case 'city':
                case 'state':
                    if(!preg_match("/^[a-z ]{0,100}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U203)");
                    }
                    break;

                case 'zip':
                    if(!preg_match("/^[a-z0-9 ]{0,20}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U204)");
                    }
                    break;

                case 'country':
                    if(!preg_match("/^[a-z ]{2,100}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U205)");
                    }
                    break;
                // case 'card_number':
                //         if(!preg_match("/[0-9]/i", $valuePost))
                //         {
                //             return $this->errorOccurredResponse("Invalid Input (VSPT-U206)");
                //         }
                //         break;

                // case 'expiration_date':
                //     if(!preg_match("/[0-9]/i", $valuePost))
                //     {
                //         return $this->errorOccurredResponse("Invalid Input (VSPT-U207)");
                //     }
                //     break;
                // case 'security_code':
                //     if(!preg_match("/[0-9]/i", $valuePost))
                //     {
                //         return $this->errorOccurredResponse("Invalid Input (VSPT-U208)");
                //     }
                //     break;

                case 'newsletter':
                case 'email_notification':
                    if(!(is_numeric($valuePost) && $valuePost <= 1 && $valuePost >= 0))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U206)");
                    }
                    break;

                default:
                    break;
            }
        }

        $reverify = $_POST['email'] != $_SESSION["uEmail"];

        // update DB //

        $user = User::withID($id, $this->db);
        
        
        $user->setFirstName($_POST["first_name"]);
        $user->setLastName($_POST["last_name"]);
        $user->setEmail($_POST["email"]);
        $user->setCountry($_POST["country"]);
        $user->setState($_POST["state"]);
        $user->setCity($_POST["city"]);
        $user->setAddress($_POST["address"]);
        $user->setZipcode($_POST["zip"]);
        $user->setNewsletter($_POST["newsletter"]);
        $user->setEmailNotification($_POST["email_notification"]);
        
        if(isset($_POST["typist"]))
        {
            $user->setTypist($_POST["typist"]);
        }

        if($reverify)
        {
            $user->setAccountStatus(USER_ACCOUNT_STATUS::PENDING_EMAIL_VERIFICATION);
        }

        $updated = $user->save();
        // var_dump(11111,$updated);

        if ($updated) {
            $this->logger->insertAuditLogEntry($this->API_NAME, "Updated User from settings: " . $id);

            if($reverify)
            {
                // re-send verification mail
                $this->mailer->sendEmail(5, $_POST["email"]);

                // logout user
                session_unset();
            }

            return $this->oKResponse($id, "User Updated");
        } else {
            return $this->errorOccurredResponse("Couldn't update user or no changes were found to update");
        }
    }

    public function updateCurrentUserZipCode()
    {
        $id = $_SESSION["uid"];

        // Only required field is Zipcode
        // This is to update the zipcode field to use for tax calculation on iOS app
        if (
            !isset($_POST["zip"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (VSPT-U400)");
        }

        // validation
        //We are validating on iOS prior to sending so this should always pass validation
        foreach ($_POST as $keyPost => $valuePost) {
            switch ($keyPost)
            {
                case 'zip':
                    if(!preg_match("/^[a-z0-9 ]{0,20}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-U204)");
                    }
                    break;
            }
        }

        // update DB //
        $user = User::withID($id, $this->db);
        $user->setZipcode($_POST["zip"]);
        $updated = $user->save();
        // var_dump(11111,$updated);

        if ($updated) {
            $this->logger->insertAuditLogEntry($this->API_NAME, "Updated User Zipcode from API: " . $id);

           return $this->oKResponse($id, "User Updated");
        } else {
            return $this->errorOccurredResponse("Couldn't update user or no changes were found to update");
        }
    }

    public function oKResponse($id, $msg2 = "")
    {

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            "error" => false,
            "msg" => $msg2,
            "id" => $id
        ]);
        return $response;

    }

    private function errorOccurredResponse($error_msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Error Occurred';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => $error_msg
        ]);
        return $response;
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM users
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            $this->logger->insertAuditLogEntry($this->API_NAME, "Deleted user id: " . $id);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    /**
     * Delete transcribe user shortcut
     * REQUEST param: name, val
     * @return mixed
     */
    public function deleteUserShortcut()
    {
        $shortcutsJson = $this->getUserShortcuts();
        $shortcutsArr = json_decode($shortcutsJson);
        $key = array_search($_REQUEST["name"], array_column($shortcutsArr, 'name'));
        unset($shortcutsArr[$key]);

        $newJson = json_encode(array_values($shortcutsArr));
        return $this->updateUserShortcuts($newJson);
    }

    /**
     * Updates transcribe user shortcuts
     * @param $shortcutsJsonString
     * @return mixed
     */
    public function updateUserShortcuts($shortcutsJsonString)
    {
        $statement = "
            UPDATE users
            SET
                shortcuts = :shortcuts
            WHERE
                id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'shortcuts' => $shortcutsJsonString,
                'id' => $_SESSION["uid"]
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }


    /**
     * Add transcribe user shortcut
     * REQUEST param: name, val
     * @return mixed
     */
    public function addUserShortcut()
    {
        $shortcutsJson = $this->getUserShortcuts();
        $shortcutsArr = json_decode($shortcutsJson);
        array_push($shortcutsArr,
            array(
                "name"=>str_replace(' ', '', $_REQUEST["name"]),
                "val"=>$_REQUEST["val"],
                "custom"=>true
            ));
        $newJson = json_encode(array_values($shortcutsArr));
        return $this->updateUserShortcuts($newJson);
    }

    public function getNewPasswordWithHash() {

        $password = $this->generateRandomPassword();
        return array(
            "pwd" => $password,
            "hash" => password_hash($password,PASSWORD_BCRYPT)
        );
    }

    function generateRandomPassword($length = 12){
        $chars = "0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
        return substr(str_shuffle($chars),0,$length);
    }

    public function insertModel(BaseModel $model): int
    {
        // TODO: Implement insertModel() method.
    }

    public function updateModel(BaseModel|User $model): int
    {
        $statement = "
            UPDATE users
            SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                password = :password,
                city = :city,
                country = :country,
                zipcode = :zipcode,
                state = :state,
                email_notification = :email_notification,
                account_status = :account_status,
                account = :account,
                typist = :typist,
                newsletter = :newsletter,
                address = :address
            WHERE
            id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => $model ->getId()  ,
                'first_name' => $model ->getFirstName()  ,
                'last_name' => $model ->getLastName()  ,
                'email' => $model ->getEmail()  ,
                'password' => $model ->getPassword()  ,
                'city' => $model ->getCity()  ,
                'country' => $model ->getCountry()  ,
                'zipcode' => $model ->getZipcode()  ,
                'state' => $model ->getState()  ,
                'account_status' => $model ->getAccountStatus()  ,
                'account' => $model ->getAccount()  ,
                'typist' => $model ->getTypist()  ,
                'email_notification' => $model ->getEmailNotification()  ,
                'newsletter' => $model ->getNewsletter() ,
                'address' => $model ->getAddress()
            ));
            // setting session variables
            $_SESSION['userData']['address'] = $model->getAddress();
            $_SESSION['userData']['city'] = $model->getCity();
            $_SESSION['userData']['state'] = $model->getState();
            $_SESSION['userData']['country'] = $model->getCountry();
            $_SESSION['userData']['zipcode'] = $model->getZipcode();
            $_SESSION['userData']['newsletter'] = $model->getNewsletter();
            $_SESSION['userData']['email_notification'] = $model->getEmailNotification();
            $_SESSION['userData']['account'] = $model->getAccount();


            $_SESSION['userData']['first_name'] = $model->getFirstName();
            $_SESSION['fname'] = $model->getFirstName();

            $_SESSION['userData']['last_name'] = $model->getLastName();
            $_SESSION['lname'] = $model->getLastName();

            $_SESSION['userData']['email'] = $model->getEmail();
            $_SESSION['uEmail'] = $model->getEmail();


            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function deleteModel(int $id): int
    {
        $statement = "
            DELETE FROM users
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
//            exit($e->getMessage());
        }
    }

    public function findModel($id): array|null
    {

        $statement = "
            SELECT 
                id,
                first_name,
                last_name,
                email,
                password,
                city,
                country,
                zipcode,
                email_notification,
                newsletter,
                account_status,
                account,
                typist,
                state,
                address                                     
            FROM
                users
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if($statement->rowCount() > 0)
            {
                return $result;
            }else{
                return null;
            }
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    // by email
    public function findAltModel($email): array|null
    {
        $statement = "
            SELECT 
                id,
                first_name,
                last_name,
                email,
                password,
                city,
                country,
                zipcode,
                email_notification,
                newsletter,
                account_status,
                account,
                state,
                typist,
                address
                                      
            FROM
                users
            WHERE
                users.email = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($email));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if($statement->rowCount() > 0)
            {
                return $result;
            }else{
                return null;
            }
        } catch (\PDOException) {
            return null;
        }
    }

    public function findAllModel($page = 1): array|null
    {
        // TODO: Implement findAllModel() method.
    }
}