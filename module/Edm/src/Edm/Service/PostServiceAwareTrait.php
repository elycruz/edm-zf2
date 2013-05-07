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
trait PostServiceAwareTrait {

    /**
     * Post Service
     * @var Edm\Service\AbstractService
     */
    protected $postService;
    
    /**
     * Post Service Name used to get the post service
     * @var string 
     */
    public $postServiceClassName = 'Edm\Service\PostService';
    
    /**
     * Gets our post service
     * @return Edm\Serivce\AbstractService
     */
    public function getPostService() {
        if (empty($this->postService)) {
            $this->postService = 
                    $this->getServiceLocator()
                        ->get($this->postServiceClassName);
        }
        return $this->postService;
    }

    
    public function setPostService(AbstractService $postService) {
        $this->postService = $postService;
    }
    
}
