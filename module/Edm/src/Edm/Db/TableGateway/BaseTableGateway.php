<?php

namespace Edm\Db\TableGateway;

use Edm\Db\TableGateway\BaseTableGatewayInterface;
use Zend\Db\TableGateway\TableGateway;

class BaseTableGateway extends TableGateway implements BaseTableGatewayInterfaceInterface {

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

    public function __construct($tableName, $tableAlias) {
        $this->table = $tableName;
        $this->alias = $tableAlias;
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new ucase($tableAlias));
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

