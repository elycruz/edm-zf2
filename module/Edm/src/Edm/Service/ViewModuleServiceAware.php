<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Service;

use Edm\Service\AbstractService;

/**
 * 
 * @author ElyDeLaCruz
 */
interface ViewModuleServiceAware {
    public function getViewModuleService ();
    public function setViewModuleService (AbstractService $viewModule);
}
