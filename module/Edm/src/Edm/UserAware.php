<?php

namespace Edm;

use Edm\Model\AbstractModel;

interface UserAware {
    public function getUser();
    public function setUser(AbstractModel $user);
}