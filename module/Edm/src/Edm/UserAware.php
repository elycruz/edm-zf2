<?php

namespace Edm;

use Zend\Authentication\Adapter as AuthAdapter,
    Edm\Service\AbstractService;

interface UserAware
{
    public function getAuthAdapter();
    public function setAuthAdapter(AuthAdapter $adapter);
    public function getUserService();
    public function setUserService(AbstractService $service);
    public function getUser();
    public function setUser($value);
}
