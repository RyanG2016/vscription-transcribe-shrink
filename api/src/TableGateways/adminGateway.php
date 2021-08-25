<?php

namespace Src\TableGateways;

//use Src\TableGateways\AccountGateway;
//require "testsFilter.php";

use Src\Helpers\common;
use Src\Models\Access;
use Src\Models\User;

class adminGateway
{

    private $db = null;
    private $accountGateway;
    private $filesGateway;
    private $srQueueGateway;
    private $common;
    private $adminUID = 4;

    public function __construct($db)
    {
        $this->db = $db;
        $this->accountGateway = new AccountGateway($db);
        $this->common = new common($db);
        $this->filesGateway = new FileGateway($db);
        $this->srQueueGateway = new srQueueGateway($db);
    }


    public function getStatistics()
    {
        // get org count
        $accounts = $this->accountGateway->getCount();

        // get files count
        $files = $this->filesGateway->getCount();

        // get files chart
        $filesChart = $this->filesGateway->getChartData();

        // get sr queue chart
        $srChart = $this->srQueueGateway->getChartData();

        // get access of default account to all orgs count
        $sysOrgAccessCount = $this->accountGateway->getSysAdminAccessCount($this->adminUID);
        $missingIDs = $this->accountGateway->getMissingSysAccessOrgIDs($this->adminUID);


        return array(
            "org_count" => $accounts,
            "sys_org_access_count" => $sysOrgAccessCount,
            "admin_access_missing_ids" => $missingIDs,
            "files_count" => $files,
            "files_chart" => $filesChart,
            "sr_chart" => $srChart
        );

    }

    /**
     * Grants access with system admin role (1) to system admin user (UID: 4) to all organizations on site
     * Used under admin panel wrench button
     * @return array
     */
    public function grantAllAccess()
    {
        $missingIDs = $this->accountGateway->getMissingSysAccessOrgIDs($this->adminUID);
        $sysAdminUser = User::withID($this->adminUID, $this->db);

        $granted = true;

        foreach ($missingIDs as $ID) {
            $status = (new Access(acc_id: $ID, uid: $this->adminUID, username: $sysAdminUser->getEmail(), acc_role: 1, db: $this->db))->save();
            if(!$status) $granted = false;
        }

        return $this->common->generateApiResponseArr($granted?'Access Granted.':'Failed please try again.', !$granted, $missingIDs);
//        return $this->common->generateApiResponseArr(true?'Access Granted.':'Failed please try again.', !true);
//        return $missingIDs;

    }

}