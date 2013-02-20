<?php

/*
 */

/**
 * Description of AbstractViewModule
 *
 * @author ElyDeLaCruz
 */
abstract class Edm_View_Helper_AbstractViewModule 
extends Edm_View_Helper_Abstract
{
    /**
     * View Module Service
     * @var Edm_Service_Internal_AbstractCrudService
     */
    protected $_viewModuleService;
            
    /**
     * View Module Service
     * @return Edm_Service_Internal_AbstractCrudService
     */
    public function getViewModuleService() {
        $vm = $this->_viewModuleService;
        if (empty($vm)) {
            if (Zend_Registry::isRegistered('edm-viewModule-service')) {
                $vm = Zend_Registry::get('edm-viewModule-service');
            } else {
                $vm = new Edm_Service_Internal_ViewModuleService();
                Zend_Registry::set('edm-viewModule-service', $vm);
            }
        }
        return $vm;
    }
}