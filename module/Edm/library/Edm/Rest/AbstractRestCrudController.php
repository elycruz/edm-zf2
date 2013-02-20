<?php

/*
 * Edm CMS - The Extensible Data/Content Management System 
 * 
 * LICENSE 
 * 
 * Copyright (C) 2011-2012  Ely De La Cruz http://www.elycruz.com
 * 
 * All rights under the GNU General Public License v3.0 or later 
 * (see http://opensource.org/licenses/GPL-3.0) and the MIT License
 * (see http://opensource.org/licenses/MIT) reserved.
 * 
 * All questions and/or comments concerning the software and its licenses 
 * can be directed to: info -at- edm -dot- elycruz -dot- com
 * 
 * If you did not received a copy of these licenses with this software
 * request a copy at: license -at- edm -dot- elycruz -dot- com
 */

/**
 * Description of AbstractCrudController
 *
 * @author ElyDeLaCruz
 */
class Edm_Rest_AbstractRestCrudController 
extends Edm_Rest_AbstractRestController
implements Edm_Db_DbAccess, Edm_Db_DbDataHelperAccess {

    /**
     * Database data helper for escaping data to and fro database
     * @var Edm_Db_DbDataHelper
     */
    protected $_dbDataHelper;

    /**
     * Database adapter
     * @var Zend_Db_AbstractAdapter
     */
    protected $_db;
    
    /**
     * Term Tax Service;  Not sure if we're keeping this one here
     * @var Edm_Service_Internal_TermTaxonomyService
     */
    protected $termTaxService;

    public function setDb(Zend_Db_Adapter_Abstract $db) {
        $this->db = $db;
    }

    public function getDb() {
        if (empty($this->db)) {
            $this->db = Zend_Db_Table::getDefaultAdapter();
        }
        return $this->db;
    }

    public function getDbDataHelper() {
        if (empty($this->_dbDataHelper)) {
            if (Zend_Registry::isRegistered('edm-dbDataHelper')) {
                $dbDataHelper = Zend_Registry::get('edm-dbDataHelper');
            }
            else {
                $dbDataHelper = new Edm_Db_DatabaseDataHelper();
                Zend_Registry::set('edm-dbDataHelper', $dbDataHelper);
            }
            $this->_dbDataHelper = $dbDataHelper;
        }
        return $this->_dbDataHelper;
    }

    public function setDbDataHelper(Edm_Db_DbDataHelper $dbDataHelper) {
        $this->_dbDataHelper = $dbDataHelper;
    }
    
    public function getTermTaxService() {
        if (empty($this->termTaxService)) {
            if (Zend_Registry::isRegistered('edm-termTax-service')) {
                $termTaxService = Zend_Registry::get('edm-termTax-service');
            }
            else {
                $this->termTaxService = 
                    $termTaxService =
                        new Edm_Service_Internal_TermTaxonomyService();
                Zend_Registry::set('edm-termTax-service', $termTaxService);
            }
        }
        return $this->termTaxService;
    }

}