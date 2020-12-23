<?php


namespace Src\TableGateways;


use Src\Models\BaseModel;

/**
 * Interface for default gateway functions
 * @package Src\TableGateways
 */
interface GatewayInterface
{

    /**
     * inserts new record to DB using $model
     * @param BaseModel $model
     * @return int
     * 0 -> false <br>
     * 1 -> true
     */
    public function insert(BaseModel $model):int;

    /**
     * update record using $model default ID
     * @param BaseModel $model
     * @return int
     * 0 -> false <br>
     * 1 -> true
     */
    public function update(BaseModel $model):int;

    /**
     * deletes a record from db using $id
     * @param int $id
     * @return int
     */
    public function delete(int $id):int;

    /**
     * get record by ID
     * @param $id
     * @return array|null
     * array of $row <br>
     * null if nothing found
     */
    public function find($id): array|null;

    /**
     * get record by foreign key ID
     * @param $id
     * @return array|null
     * array of $row <br>
     * null if nothing found
     */
    public function findAlt($id): array|null;

    /**
     * get all records limited by $limit
     * @param int $page
     * @return array|null
     * array of $row <br>
     * null if nothing found
     */
    public function findAll($page = 1): array|null;
}