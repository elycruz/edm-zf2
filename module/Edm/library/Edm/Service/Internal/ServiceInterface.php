
<?php
/**
 * @author ElyDeLaCruz
 */
interface Edm_Service_Internal_ServiceInterface
{
    public function getByColumn($columnName, $columnValue,
            $tableAlias, $fetchMode = Zend_Db::FETCH_OBJ);
    public function getSelect();
}