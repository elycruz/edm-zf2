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
interface PageServiceAware {
    public function getPageService();
    public function setPageService(AbstractService $postService);
}
