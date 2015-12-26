<?php

namespace Edm\Auth;

use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Assumes a service locator aware interface is being used in parent class.
 * @author ElyDeLaCruz
 */
interface AuthServiceAware {
    public function getAuthService ();
    public function setAuthService (AuthenticationServiceInterface $authService);
}
