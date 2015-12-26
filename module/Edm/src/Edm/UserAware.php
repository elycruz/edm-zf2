<?php

//declare(strict_types=1);

namespace Edm;

use Edm\Db\ResultSet\Proto\UserProto;

interface UserAware {
    public function getUser(); // :UserProto;
    public function setUser(UserProto $user);
}
