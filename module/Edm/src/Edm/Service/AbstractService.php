<?php

namespace Edm\Service;

use Edm\Db\DbAware,
    Edm\Db\DbDataHelperAware,
    Edm\TraitPartials\DbAwareTrait,
    Edm\TraitPartials\ServiceLocatorAwareTrait,
    Edm\TraitPartials\DbDataHelperAwareTrait,
    \stdClass,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql;

abstract class AbstractService implements
ServiceLocatorAwareInterface, DbDataHelperAware, DbAware {

    use ServiceLocatorAwareTrait,
        DbDataHelperAwareTrait,
        DbAwareTrait;

    const FETCH_FIRST_ITEM = 0;
    const FETCH_RESULT_SET = 1;
    const FETCH_RESULT_SET_TO_ARRAY = 3;

    /**
     * Services Sql Object
     * @var Zend\Db\Sql\Sql
     */
    protected $sql;

    /**
     * Return Options as stdClass
     * @param mixed $options
     * @return \stdClass
     */
    public function normalizeMethodOptions($options = null) {
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
    public function seedOptionsForSelect(stdClass $options) {
        // Sql
        $sql = !empty($options->sql) ? $options->sql : $this->getSql();

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
                $options->fetchMode : self::FETCH_FIRST_ITEM;
        
        // Send some prelims to user
        $options->select = $select;
        $options->sql = $sql;

        return $options;
    }

    /**
     * Read from db using "get select" and "get sql"
     * @param mixed $options
     * @return mixed array result | array
     */
    public function read($options = null) {
        // Normalize/get options object and seed it with default select params
        $options = $this->seedOptionsForSelect(
                $this->normalizeMethodOptions($options));

        // Get results
        $rslt = $this->resultSet->initialize(
                $options->sql->prepareStatementForSqlObject(
                        $options->select)->execute());

        return $this->fetchFromResult($rslt, $options->fetchMode);
    }
    
    /**
     * Returns an Sql object seeded with the global db adapter 
     * for the edm module
     * @return Zend\Db\Sql\Sql
     */
    protected function getSql() {
        if (empty($this->sql)) {
            $this->sql = new Sql($this->getDb());
        }
        return $this->sql;
    }

    /**
     * 
     */
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
        switch ($fetchMode) {
            case self::FETCH_FIRST_ITEM:
                return $rslt->current()->exchangeArray(
                        $dbDataHelper->reverseEscapeTuple(
                                $rslt->current()->toArray()));
                break;
            case self::FETCH_RESULT_SET:
                return (new ResultSet())->initialize($rslt);
                break;
            case self::FETCH_RESULT_SET_TO_ARRAY: 
            default: 
                return $this->cleanResultSetToArray($rslt);
                break;
        }
    }
    
}