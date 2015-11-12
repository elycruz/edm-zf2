<?php

namespace Edm\Db\TableGateway;

use Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareInterface;

class BaseTableGateway extends TableGateway implements
    BaseTableGatewayInterface,
    ServiceLocatorAwareInterface {

    use ServiceLocatorAwareTrait;

    /**
     * Table Alias 
     * ** Should be the same as the extending classes name camel cased 
     * without the trailing "Table";  I.e.,  for the "UserTable" class the 
     * alias should be "user" this allows services to get tables and models by alias and
     * allows sql to have an oop nature by having columns that point to these
     * tables/models by aliases as well.  This also allows us to have more succinct sql
     * when refering to multiple tables in query;  E.g., instead of: SELECT term_alias.term_taxonomies, ...
     * we can write: 'SELECT term_alias.termTaxonomy' (makes our life a little easier)
     * also now when fetching our Model (if needed) we can now do
     *  `$model = new (ucase($table->alias))(); `  and walla we have our model without explicitly knowing
     * or if our model had the word 'Model' appended to it then:
     *   `$model = new (ucase($table->alias) . 'Model')();` Walla!
     * @var string
     */
    protected $alias;

    /**
     * String of model class to pass as a prototype to the result set object for this table.
     * @var \string
     */
    protected $modelClass;

    /**
     * Table prefix if any (appended to default table name
     * if this class is extended or if you pass in a `$tableName`).
     * @var \string
     */
    protected $tablePrefix;

    public function __construct($tableNamePrefix = null, $tableName = null, $tableAlias = null) {

        if ($tableName) {
            $this->table = $tableName;
        }

        if ($tableAlias) {
            $this->alias = $tableAlias;
        }

        if ($tableNamePrefix) {
            $this->tablePrefix = $tableNamePrefix;
            $this->table = $tableNamePrefix . $this->table;
        }

        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new $this->modelClass());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function getFirstBy(array $by) {
        return $this->select($by)->current();
    }

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias(\string $alias) {
        $this->alias = $alias;
        return $this;
    }

}

