<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Auth;

use Zend\Authentication\Adapter\AdapterInterface;

/**
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
interface AuthServiceAware {
    public function getAuthService ();
    public function setAuthService (AdapterInterface $authService);
}
