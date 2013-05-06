<?php

namespace Edm;

interface UserAware {
    public function getUser();
    public function setUser(\stdClass $user);
}