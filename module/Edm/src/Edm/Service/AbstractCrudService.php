<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/15/2015
 * Time: 8:40 PM
 */

namespace Edm\Service;

use Edm\Db\DbAware,
    Edm\Db\DbDataHelperAware,
    Edm\Db\DbAwareTrait,
    Edm\ServiceManager\ServiceLocatorAwareTrait,
    Edm\Db\DbDataHelperAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\Db\Sql\Sql,
    \stdClass;

abstract class AbstractCrudService implements
    ServiceLocatorAwareInterface, DbDataHelperAware, DbAware {

    use ServiceLocatorAwareTrait,
        DbDataHelperAwareTrait,
        DbAwareTrait;

    /**
     * @var \Zend\Db\ResultSet\ResultSet
     */
    protected $resultSet;

    /**
     * Return Options as stdClass
     * @param stdClass|ArrayObject|array $options
     * @return \stdClass
     */
    public function normalizeMethodOptions($options = null) {
        // Expect stdObject|Object as options else converts them
        if (is_array($options)) {
            $options = (object) $options;
        }
        else if (!is_object($options) || !(is_a($options, 'ArrayObject')
            || is_a($options, 'stdClass'))) {
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
        $sql = isset($options->sql) ? $options->sql : $this->getSql();

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

        // Send some prelims to user
        $options->select = $select;
        $options->sql = $sql;

        return $options;
    }

    /**
     * Read from db using "get select" and "get sql"
     * @param mixed $options
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function read($options = null) {
        // Normalize/get options object and seed it with default select params
        $options = $this->seedOptionsForSelect(
            $this->normalizeMethodOptions($options));

        // Get results
        return $this->resultSet->initialize(
            $options->sql->prepareStatementForSqlObject(
                $options->select)->execute());
    }

    /**
     * Returns an Sql object seeded with the global db adapter
     * for the edm module
     * @return \Zend\Db\Sql\Sql
     */
    public function getSql() {
        return new Sql($this->getDb());
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    abstract public function getSelect();

}