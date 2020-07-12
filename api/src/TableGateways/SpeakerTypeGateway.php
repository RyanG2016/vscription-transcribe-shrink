<?php

namespace Src\TableGateways;

require "filters/speakerTypesFilter.php";

class SpeakerTypeGateway
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($parseForComboBox = false)
    {
        $filter = parseParams(true);

        if($parseForComboBox)
        {
            $statement = "
            SELECT 
                id as 'value', name as 'label'
            FROM
                file_speaker_type
        " . $filter . ";";
        } else {
            $statement = "
            SELECT 
                *
            FROM
                file_speaker_type
        " . $filter . ";";
        }

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION['accID']));
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
                file_speaker_type
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

    public function insertNewSpeakerTypes($name)
    {

        $statement = "INSERT
                        INTO 
                            file_speaker_type 
                            (
                             name
                             ) 
                         VALUES 
                                (?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($name));

            if ($statement->rowCount()) {
                return true;
            } else {
                return false;
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
        }

    }

  /*  public function delete($id)
    {
        $statement = "
            DELETE FROM speakerTypes
            WHERE speakerTypes_id = :id;
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