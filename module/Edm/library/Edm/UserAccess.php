<?php

/**
 * Description of UserAccess
 *
 * @author ElyDeLaCruz
 */
interface Edm_UserAccess
{
    public function getAuthAdapter();
    public function setAuthAdapter(Zend_Auth $value);
    public function getUserService();
    public function setUserService(
            Edm_Service_Internal_AbstractService $value);
    public function getUser();
    public function setUser($value);
}
