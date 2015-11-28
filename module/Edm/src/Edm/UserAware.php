<?php

namespace Edm;

use Edm\Db\ResultSet\Proto\UserProto;

interface UserAware {
    public function getUser();
    public function setUser(UserProto $user);
}
