<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Db;
use Zend\Db\Adapter\Adapter as DbAdapter,
 Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

/**
 * Assumes access to service manager and service locator aware interface
 * @author ElyDeLaCruz
 */
trait DbAwareTrait {
    
    protected $db;
    
    public function setDb(DbAdapter $db) {
        $this->db = $db;
    }
    
    /**
     * Get database adapter
     * @return Zend\Db\Adapter\Adapter
     */
    public function getDb() {
        if (empty($this->db)) {
            $this->db = GlobalAdapterFeature::getStaticAdapter();
        }
        return $this->db;
    }
}
