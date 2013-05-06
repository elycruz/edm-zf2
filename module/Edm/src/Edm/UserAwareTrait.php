<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm;

use Edm\Auth\AuthServiceAwareTrait;

/**
 * Getter and Setters for a user std object.  
 * @uses AuthServiceAwareTrait
 * @author ElyDeLaCruz
 */
trait UserAwareTrait {

    use AuthServiceAwareTrait;

    /**
     * User variable
     * @var Edm\Model\User
     */
    protected $user = null;

    /**
     * Gets a user
     * @return \stdClass
     */
    public function getUser() {
        $authService = $this->getAuthService();
        if (empty($this->user)) {
            if ($authService->hasIdentity()) {
                $this->user = $authService->getIdentity();
            }
        }
        return $this->user;
    }

    /**
     * Set our user
     * @param \stdClass $user
     */
    public function setUser(\stdClass $user) {
        $this->user = $user;
    }

}

