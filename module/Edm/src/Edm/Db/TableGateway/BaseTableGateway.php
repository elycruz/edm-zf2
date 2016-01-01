<?php

namespace Edm\Db\TableGateway;

use Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway,
    Zend\Db\TableGateway\Exception\InvalidArgumentException;

class BaseTableGateway extends TableGateway {

    /**
     * Table Alias 
     * ** Should be the same as the extending classes name camel cased 
     * without the trailing "Table";  I.e.,  for the "UserTable" class the 
     * alias should be "user" this allows services to get tables and models by alias and
     * allows sql to have an oop nature by having columns that point to these
     * tables/models by aliases as well.  This also allows us to have more succinct sql
     * when referring to multiple tables in query;  E.g., instead of: SELECT term_alias.term_taxonomies, ...
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
     * BaseTableGateway constructor for simplifying creation of tables created in for .
     * Assumes `table`, `alias` and `modelClass` to be non-null or already set (in definition).
     */
    public function __construct () {
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new $this->modelClass());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    /**
     * __get - Overridden @see parent class.
     *
     * @param  string $property
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     * @return mixed
     */
    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'lastinsertvalue':
                return $this->lastInsertValue;
            case 'adapter':
                return $this->adapter;
            case 'table':
                return $this->table;
            case 'alias':
                return $this->alias;
            case 'modelclass':
                return $this->modelClass;
        }
        if ($this->featureSet->canCallMagicGet($property)) {
            return $this->featureSet->callMagicGet($property);
        }
        throw new InvalidArgumentException('Invalid magic property access in ' . 
                __CLASS__ . '::__get()');
    }

}

