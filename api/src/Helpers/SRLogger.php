<?php


namespace Src\Helpers;


/**
 * Class SRLogger
 * Adds a log entry to sr_log tbl
 * @package Src\Helpers
 */
class SRLogger
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param int $srq_id SR Queue ID
     * @param ?int $file_id File ID
     * @param String $srlog_activity @SRLOG_ACTIVITY enum
     * @param String|null $msg optional msg string
     * @return int
     */
    public function log(
        ?int $srq_id,
        ?int $file_id,
        String $srlog_activity,
        String $msg = null
    )
    {
        $statement = "INSERT INTO sr_log(srq_id, file_id, srlog_activity, srqlog_msg) 
                        VALUES
                               (
                                ?, ?, ?, ?
                               )";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $srq_id, $file_id, $srlog_activity, $msg
            ));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }
}