<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm;

use Edm\Auth\AuthServiceAwareTrait,
    Edm\Db\ResultSet\Proto\UserProto;

/**
 * Getter and Setters for a user std object.  
 * @uses AuthServiceAwareTrait
 * @author ElyDeLaCruz
 */
trait UserAwareTrait {

    use AuthServiceAwareTrait;

    /**
     * User variable
     * @var \Edm\Db\ResultSet\Proto\
     */
    protected $user = null;

    /**
     * Gets a user
     * @return \Edm\Db\ResultSet\Proto\UserProto
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
     * @param \Edm\Db\ResultSet\Proto\UserProto
     * @return \Edm\UserAware;
     */
    public function setUser(UserProto $user) {
        $this->user = $user;
        return $this;
    }

}
