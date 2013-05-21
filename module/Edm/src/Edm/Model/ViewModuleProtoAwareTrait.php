<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Model;

use Edm\Model\ViewModule;

/**
 * Description of HasViewModuleTrait
 * @overrides exchangeArray
 * @author ElyDeLaCruz
 */
trait ViewModuleProtoAwareTrait {
    
    /**
     * View Module Prototoype Model
     * @var Edm\Model\ViewModule
     */
    public $viewModuleProto;
    
    /**
     * Set view module proto
     * @param \Edm\Model\ViewModule $proto
     * @return Edm\Model\AbstractModel
     */
    public function setViewModuleProto (ViewModule $proto) {
        $this->viewModuleProto = $proto;
        return $this;
    }
    
    /**
     * Get view module proto
     * @return Edm\Model\AbstractModel
     */
    public function getViewModuleProto () {
        if (empty($this->viewModuleProto)) {
            $this->viewModuleProto = new ViewModuleProto();
        }
        return $this->viewModuleProto;
    }
    
    /**
     * Exchange array overriden to divide data between implementer proto model and view module proto model
     * @param array $data
     * @return \Edm\Model\AbstractModel
     */
    public function exchangeArray(array $data) {
        $viewModule = $this->getViewModuleProto();
        $viewModuleValidKeys = $viewModule->getValidKeys();
        foreach ($data as $key => $val) {
            if (in_array($key, $this->validKeys)) {
                $this->{$key} = $val;
            }
            else if (in_array($key, $viewModuleValidKeys)) {
                $viewModule->{$key} = $val;
            }
        }
        $this->viewModuleProto = $viewModule;
        return $this;
    }
}

