<?php

namespace Edm\Db\Table;

use Edm\Db\Table\AbstractTable,
    Edm\Model\ViewModule,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

/**
 * Description of ViewModuleTable
 *
 * @author ElyDeLaCruz
 */
class ViewModuleTable extends AbstractTable {

    protected $table = 'view_modules';

    protected $alias = 'viewModule';

    public function __construct() {
        $this->table = 'view_modules';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new ViewModule());
        $this->resultSetPrototype = $resultSetProto;
        $this->initialize();
    }

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
    }
}
