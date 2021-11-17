<?php
namespace Src\Helpers;

require_once __DIR__ . "/../../bootstrap.php";
use Src\Enums\ROLES;
use Src\Enums\SESSION_SRC;
use Src\Models\Session;
use Src\Models\User;
use Src\TableGateways\logger;
use Src\TableGateways\sessionGateway;

class sessionHelper{

    const API_NAME = "Session_Helper";

    public $sessionGateway;
    private logger $logger;
    public common $common;

    public function __construct($db)
    {
        $this->db = $db;
        $this->sessionGateway = new sessionGateway($db);
        $this->logger = new logger($db);
        $this->common = new common();
    }


    function findSessExpiry() : array
    {
        if(!isset($_SESSION['loggedIn'])) return $this->formatExpiryResponse(true, 0);

        $sess = Session::withPhpSessID(session_id(), $this->db);
        if(!$sess) return $this->formatExpiryResponse(true, 0);

        $expired = $this->isExpired($sess->getExpireTime());

        if($sess->getRevoked() || $expired) // session is revoked - kick user out
        {
            // logout user immediately - done by JS responding to this response

            // logout user session manually - PHP (here)
            unset($_SESSION['loggedIn']);
//            $this->common->logout();
        }


        return $this->formatExpiryResponse(
            $expired,
            $sess->getExpireTime(),
            $sess->getRevoked()
        );

    }


    function extendSession() : array
    {
        $sess = Session::withPhpSessID(session_id(), $this->db);

        $expired = $this->isExpired($sess->getExpireTime());

        if(
            !isset($_SESSION['loggedIn']) ||
            !$sess ||
            $expired ||
            $sess->getRevoked() == 1
        ){
            unset($_SESSION['loggedIn']);
            return $this->formatExtendResponse(false, 0, 'Session already expired.');
        }

        // else extend session
        $newExDate = date('Y-m-d H:i:s', strtotime($sess->getExpireTime() . SESSION_SRC::EXTEND_AMOUNT));
        $sess->setExpireTime($newExDate);
        $sess->save(); // save to DB

        $_SESSION['sess_expire_at'] = $newExDate; // update user session var

        $this->logger->insertAuditLogEntry(self::API_NAME, 'Session ('.session_id().') extended by '.SESSION_SRC::EXTEND_AMOUNT);


        return $this->formatExtendResponse(
            true,
            $newExDate,
            'Session extended by '.SESSION_SRC::EXTEND_AMOUNT
        );

    }

    function revokeSessionVars($phpSessID)
    {
        // save current
        $currentSessID = session_id();
        session_write_close();

        // modify next
        session_id($phpSessID);
        session_start(['cookie_lifetime' => 31536000,'cookie_secure' => true,'cookie_httponly' => true]); // 1 Year

        session_unset();
        session_write_close(); // save data

        // restore
        session_id($currentSessID);
        session_start(['cookie_lifetime' => 31536000,'cookie_secure' => true,'cookie_httponly' => true]); // 1 Year

    }

    function revokeAccess($sessID)
    {
        if($sessID)
        {
            $sess = Session::withID($sessID, $this->db);

            if($sess && $sess->getUid() == $_SESSION['uid'])
            {

                $sess->setRevoked(1);
                $sess->setRevokeDate(date("Y-m-d H:i:s"));
                $sess->save();

                // revoke session variables
                $this->revokeSessionVars($sess->getPhpSessId());

                $this->logger->insertAuditLogEntry(self::API_NAME, 'Access revoked to session: ' . $sess->getPhpSessId() . ' | from: ' . session_id());
                return $this->formatRevokeResponse(true, 'Session ' . $sess->getId() . ' has been revoked.');
            }else{
                // you are not the account owner - shown as session not found for safety
                return $this->formatRevokeResponse(false, 'Session not found.');
            }
        }
        return $this->formatRevokeResponse(false, 'Invalid request.');
    }

    /**
     * Used in ping.php to double check if session expired from db record before logging user out
     * @param $phpSessID
     * @return bool
     */
    function isExpiredFromDB($phpSessID) : bool
    {
        $sess = Session::withPhpSessID($phpSessID, $this->db);
        if(!$sess) return true;


        $expired = $this->isExpired($sess->getExpireTime());

        if(!$expired){
            // update session expiry variable
            $_SESSION['sess_expire_at'] = $sess->getExpireTime();
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param string $expiryDate
     * @return bool true if expired
     */
    function isExpired(string $expiryDate): bool
    {
        return strtotime($expiryDate) < time();
    }

    function formatExpiryResponse(bool $expired, $expiryDate, int $revoked = 0): array
    {
        return array(
            'session' => session_id()??'',
            'revoked' => $revoked,
            'expired' => $expired,
            'expire_date' => $expiryDate,
            'time_left' => $expired?0: strtotime($expiryDate) - time()
        );
    }


    function formatExtendResponse(bool $extended, $newExpiryDate, $msg = ''): array
    {
        return array(
            'extended' => $extended,
            'new_expire_date' => $newExpiryDate,
            'msg' => $msg
        );
    }


    function formatRevokeResponse(bool $revoked, $msg = ''): array
    {
        return array(
            'revoked' => $revoked,
            'msg' => $msg
        );
    }

}