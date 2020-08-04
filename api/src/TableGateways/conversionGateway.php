<?php

namespace Src\TableGateways;

require "filters/conversionFilter.php";

class conversionGateway
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // 0: pending, 1: done, 3: failed
    // get all pending conversions sorted by FIFO
    public function findAll($limitToOne = false)
    {
//        $filter = parseParams(false);
//        $filter = "";
        $limit = "";
        if($limitToOne)
        {
            $limit = " LIMIT 1";
        }


        $statement = "
            SELECT 
                conversion.*,
                   filename,
                   org_ext,
                   orig_filename,
                   tmp_name,
                   status,
                   file_status
            FROM
                conversion
            
            INNER JOIN files f on conversion.file_id = f.file_id
            
            WHERE
                  status = 0
            ORDER BY id 
            ;" . $limit;


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
            exit($e->getMessage());
        }
    }

    public function find($id)
    {

        $statement = "
            SELECT 
                *
            FROM
                conversion
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insertNewConversion($file_id)
    {

        $statement = "INSERT
                        INTO 
                            conversion 
                            (
                             file_id, status
                             ) 
                         VALUES 
                                (
                                 ?,?
                                )";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $file_id,
                0 // pending conversion
            ));

            if ($statement->rowCount()) {
//                return true;
                return $this->formatResult("Convert Record Created", false);
            } else {
//                return false;
                return $this->formatResult("Failed to create convert record", true);
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
//            return false;
            return $this->formatResult("Failed to create convert record (2)", true);
        }

    }

    public function formatResult($msg, $error)
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array(
            "msg" => $msg,
            "error" => $error
        ));
        return $response;
    }

    public function updateConversionStatus($file_id)
    {

        $new_status = $_POST['status'];
        if(!is_numeric($new_status))
        {
            return $this->formatResult("Failed to update record - wrong params", true);
        }else{
            switch ($new_status){
                case 0:
                case 1:
                case 3:
                    // pass
                    break;

                default:
                    return $this->formatResult("Failed to update record - wrong params", true);
                    break;
            }
        }

        $statement = "UPDATE conversion
            SET status = ?
            WHERE file_id = ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $new_status,
                $file_id
            ));

            if ($statement->rowCount()) {
//                return true;
                return $this->formatResult("Convert Record Updated", false);
            } else {
//                return false;
                return $this->formatResult("Failed to update convert record", true);
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
//            return false;
            return $this->formatResult("Failed to update convert record (2)", true);
        }

    }

    // direct update - used only with conversionCron.php
    public function updateConversionStatusFromParam($file_id, $new_status)
    {

        $statement = "UPDATE conversion
            SET status = ?
            WHERE file_id = ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $new_status,
                $file_id
            ));

            if ($statement->rowCount()) {
                return true;
//                return $this->formatResult("Convert Record Updated", false);
            } else {
                return false;
//                return $this->formatResult("Failed to update convert record", true);
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
//            return $this->formatResult("Failed to update convert record (2)", true);
        }

    }

  /*  public function delete($id)
    {
        $statement = "
            DELETE FROM conversions
            WHERE conversions_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }*/
}