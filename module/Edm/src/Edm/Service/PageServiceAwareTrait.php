<?php

namespace Edm\Service;

use Edm\Service\AbstractService;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
trait PageServiceAwareTrait {

    /**
     * Page Service
     * @var Edm\Service\AbstractService
     */
    protected $pageService;
    
    /**
     * Page Service Name used to get the page service
     * @var string 
     */
    public $pageServiceClassName = 'Edm\Service\PageService';
    
    /**
     * Gets our page service
     * @return Edm\Serivce\AbstractService
     */
    public function getPageService() {
        if (empty($this->pageService)) {
            $this->pageService = 
                    $this->getServiceLocator()
                        ->get($this->pageServiceClassName);
        }
        return $this->pageService;
    }

    
    public function setPageService(AbstractService $pageService) {
        $this->pageService = $pageService;
    }
    
}
