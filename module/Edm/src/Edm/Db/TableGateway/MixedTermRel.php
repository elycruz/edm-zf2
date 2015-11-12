<?php

namespace Edm\Db\TableGateway;

use Edm\Db\TableGateway\BaseTableGateway,
    Edm\Model\MixedTermRel,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

/**
 * Description of MixedTermRel
 *
 * @author ElyDeLaCruz
 */
class MixedTermRelTable extends BaseTableGateway {

    protected $table = 'mixed_term_relationships';
    
    protected $alias = 'mixedTermRel';

    public function __construct() {
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $resultSetProto = new ResultSet();
        $resultSetProto->setArrayObjectPrototype(new MixedTermRel());
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
