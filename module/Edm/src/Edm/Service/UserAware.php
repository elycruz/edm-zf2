<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Service;

use Edm\Service\AbstractService;

/**
 * Description of UserAware
 *
 * @author ElyDeLaCruz
 */
interface UserAware {
    public function setUserService (AbstractService $service);
    public function getUserService ();
}

