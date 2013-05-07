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
interface PostServiceAware {
    public function getPostService();
    public function setPostService(AbstractService $postService);
}
