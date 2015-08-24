<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Model;

use Edm\Model\ViewModule;

/**
 * Description of HasViewModuleTrait
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

}

