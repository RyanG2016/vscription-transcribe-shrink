<?php

namespace Src\TableGateways;

require "filters/citiesFilter.php";

class CityGateway
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($parseForComboBox = false)
    {
        $filter = parseCitiesParams(true);

        if ($parseForComboBox) {
            $statement = "
            SELECT 
                id as 'value', city as 'label'
            FROM
                cities
        " . $filter . ";";
        } else {
            $statement = "
            SELECT 
                *
            FROM
                cities
        " . $filter . ";";
        }

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


    /**
     * Retrieves cities for a given *COUNTRY* ID
     * used in landing page
     * @param $id int country ID
     * @param bool $parseForComboBox
     * @return mixed
     */
    public function find($id, $parseForComboBox = false) // $id is country id
    {
        if ($parseForComboBox) {
            $statement = "
            SELECT 
                id as 'value', city as 'label', country
            FROM
                cities
            WHERE country = ?;
        ";


        } else {
            $statement = "
            SELECT 
                *
            FROM
                cities
            WHERE country = ?;
        ";
        }
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    /**
     * Retrieves city data for a given *city* ID
     * used in sign-up API functions to check if the city id exists
     * @param $id int city ID
     * @return array array of city object or empty array
     */
    public function getCity($id) // $id is country id
    {
        $statement = "
            SELECT 
                *
            FROM
                cities
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    /*
        public function insertNewCity($name)
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
                DELETE FROM cities
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