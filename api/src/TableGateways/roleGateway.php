<?php

namespace Src\TableGateways;

use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\Role;

require "filters/rolesFilter.php";

class roleGateway implements GatewayInterface
{

    private $db;
    private $common;
    private $limit = 10;

    public function __construct($db)
    {
        $this->db = $db;
        $this->common = new common();
    }

    public function findAll()
    {
        $filter = parseRolesParams(false);

        //where role_id != 1 && roles.role_id != 6
        $statement = "
            SELECT 
                *
            FROM
                roles
            where role_id = 2 or roles.role_id = 3 or roles.role_id = 5
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

    public function insertNewroles($name)
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
            DELETE FROM roles
            WHERE roles_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }*/
    public function insertModel(BaseModel|Role $model): int
    {

        $statement = "
            INSERT INTO roles 
                (role_name, role_desc)
            VALUES
                (:role_name, :role_desc)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'role_name' => $model->getRoleName(),
                'role_desc' => $model->getRoleDesc()
            ));
            if($statement->rowCount())
            {
                return $this->db->lastInsertId();
            }else{
                return 0;
            }
        } catch (\PDOException) {
            return 0;
        }
    }

    public function updateModel(BaseModel|Role $model): int
    {
        $statement = "
            UPDATE roles
            SET
                role_name = :role_name,
                role_desc = :role_desc
            WHERE
                role_id = :role_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'role_id' => $model->getRoleId(),
                'role_name' => $model->getRoleName(),
                'role_desc' => $model->getRoleDesc()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function deleteModel(int $id): int
    {
        $statement = "
            DELETE FROM roles
            WHERE role_id = :id;
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
                *            
            FROM
                roles
            WHERE role_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function findAltModel($id): array|null
    {
        return null;
    }

    public function findAllModel($page = 1): array|null
    {

        $offset = $this->common->getOffsetByPageNumber($page, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                sr_packages
            LIMIT :limit
            OFFSET :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(":limit",$this->limit, \PDO::PARAM_INT);
            $statement->bindParam(":offset",$offset, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }
}