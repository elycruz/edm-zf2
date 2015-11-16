<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/15/2015
 * Time: 8:42 PM
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

    /**
     * @param DbAdapter $db
     * @return DbAware
     */
    public function setDb(DbAdapter $db) {
        $this->db = $db;
    }

    /**
     * Get database adapter
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDb() {
        if (empty($this->db)) {
            $this->db = GlobalAdapterFeature::getStaticAdapter();
        }
        return $this->db;
    }
}