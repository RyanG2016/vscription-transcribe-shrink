<?php

namespace Src\TableGateways;

require "filters/countriesFilter.php";

class CountryGateway
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
                id as 'value', country as 'label'
            FROM
                countries
        " . $filter . ";";
        } else {
            $statement = "
            SELECT 
                *
            FROM
                countries
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
                countries
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
/*
    public function insertNewCountry($name)
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

    public function delete($id)
    {
        $statement = "
            DELETE FROM countries
            WHERE id = :id;
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