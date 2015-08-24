<?php

namespace Edm\Service;

use Edm\Db\DbAware,
    Edm\Db\DbDataHelperAware,
    Edm\Db\DbAwareTrait,
    Edm\ServiceManager\ServiceLocatorAwareTrait,
    Edm\Db\DbDataHelperAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    \stdClass;

abstract class AbstractService implements
    ServiceLocatorAwareInterface, DbDataHelperAware, DbAware {

    use ServiceLocatorAwareTrait,
        DbDataHelperAwareTrait,
        DbAwareTrait;

    const FETCH_FIRST_AS_ARRAY_OBJ = 1;
    const FETCH_FIRST_AS_ARRAY = 2;
    const FETCH_RESULT_SET = 3;
    const FETCH_RESULT_SET_TO_ARRAY = 4;

    /**
     * Db Result Set.
     * @var Zend\Db\ResultSet\ResultSet
     */
    protected $resultSet;

    /**
     * Return Options as stdClass
     * @param mixed $options
     * @return \stdClass
     */
    public function normalizeSeedOptions($options = null) {
        // Expect Array Object as options else convert
        if (is_array($options)) {
            $options = (object) $options;
        } 
        else {
            $options = new stdClass();
        }
        return $options;
    }

    /**
     * Returns a prepared Select statement based on options:
     * {select, sql, order, where} (@todo more options to come later)
     * @param \stdClass $options
     * @return \stdClass
     */
    public function seedOptionsForSelect($options) {
        // Sql
        $sql = !empty($options->sql) ? $options->sql : new Sql($this->getDb());

        // Select
        if (isset($options->select)) {
            $select = $options->select;
        } else {
            $select = $this->getSelect($sql);
        }

        // Where
        if (isset($options->where)) {
            $select->where($options->where);
        }

        // Order
        if (isset($options->order)) {
            $select->order($options->order);
        }
        
        // Fetch mode
        $options->fetchMode = isset($options->fetchMode) ? 
                $options->fetchMode : self::FETCH_FIRST_AS_ARRAY;
        
        // Send some prelims to user
        $options->select = $select;
        $options->sql = $sql;

        return $options;
    }

    /**
     * Read from db using "get select" and "get sql"
     * @param mixed|array|stdClass $options
     * @return  Zend\Db\ResultSet\ResultSet
     */
    public function read($options = null) {
        $options = $this->seedOptionsForSelect($this->normalizeSeedOptions($options));
        return $this->resultSet->initialize($options->sql->prepareStatementForSqlObject(
                $options->select)->execute());
    }

    abstract protected function getSelect();

    /**
     * Returns a cleaned result set as array
     * @param \Zend\Db\ResultSet\ResultSet $rslt
     * @return array
     */
    public function cleanResultSetToArray(ResultSet $rslt) {
        return $this->getDbDataHelper()->reverseEscapeTuples($rslt->toArray());
    }
    
    /**
     * Fetch items/item from result set object
     * @param \Zend\Db\ResultSet\ResultSet $rslt
     * @param type $fetchMode
     * @return mixed array result | array
     */
    public function fetchFromResult (ResultSet $rslt, $fetchMode = self::FETCH_RESULT_SET_TO_ARRAY) {
        $dbDataHelper = $this->getDbDataHelper();
        $retVal = null;

        // Is current index in result set valid
         $validRslt = $rslt->valid();
         if (!$validRslt) {
             return null;
         }
         $current = $rslt->current();
         if (empty($current)) {
             return null;
         }

        // Get data
        $data = $current->toArray();

        // Resolve fetchmode
        switch ($fetchMode) {
            case self::FETCH_FIRST_AS_ARRAY:
                    $current->exchangeArray($dbDataHelper->reverseEscapeTuple($data));
                    $retVal = $current->toArray();
                break;
            case self::FETCH_FIRST_AS_ARRAY_OBJ:
                    // Clean current
                    $current->exchangeArray($dbDataHelper->reverseEscapeTuple($data));
                    $retVal = $current;
                break;
            case self::FETCH_RESULT_SET:
                    $retVal = (new ResultSet())->initialize($rslt);
                break;
            case self::FETCH_RESULT_SET_TO_ARRAY:
            default:
                $retVal = $this->cleanResultSetToArray($rslt);
            break;
        }

        return $retVal;
    }

    /**
     * Factory for generating `Sql` objects using edm's db adapter.
     * @return Zend\Db\Sql\Sql
     */
    public function sql () {
        return new Sql($this->getDb());
    }
    
}