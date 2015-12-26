<?php

//declare(strict_types=1);

namespace Edm;

use Edm\Db\ResultSet\Proto\UserProto,
    Edm\Auth\AuthServiceAware;

interface UserAware extends AuthServiceAware {
    public function getUser(); // :UserProto;
    public function setUser(UserProto $user);
}
