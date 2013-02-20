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
namespace Edm\Controller\Action;
use Edm\Controller\Action\AbstractCrudController\AbstractCrudController;
class AbstractViewModuleController
extends AbstractCrudController {

    protected $_viewModuleService;
    
    public function getViewModuleService() {
        if (empty($this->_viewModuleService)) {
            if (Zend_Registry::isRegistered('edm-viewModule-service')) {
                $viewModuleService = Zend_Registry::get('edm-viewModule-service');
            }
            else {
                $this->_viewModuleService = 
                    $viewModuleService =
                        new Edm_Service_Internal_viewModuleonomyService();
                Zend_Registry::set('edm-viewModule-service', $viewModuleService);
            }
        }
        return $this->_viewModuleService;
    }
    
    /**
     * Returns the `allowed pages` payload to a collection of tuples
     * @param array $allowedPages
     * @return mixed array | void 
     */
    protected function allowedPagesToTuples(array $allowedPages)  {
        
        // Bail if no items
        $allowedPagesCount = count($allowedPages);
        if (empty($allowedPagesCount)) {
            return;
        }
        
        //
        $output = array();
        foreach ($allowedPages as $val) {
            $parts = explode('|', $val);
            $id = $parts[0];
            $uri = $parts[1];
            $output[] = array('link_id' => $id, 'value' => $uri);
        }
        return $output;
    }
}