<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/27/2015
 * Time: 9:25 PM
 */
class PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @param string $password
     * @return string - Encoded password.
     */
    public function encodeUserPassword ($password) {
        return $this->getHasher()->create_hash($password);
    }

    /**
     * Our password and activation key hasher.
     * @return Pbkdf2Hasher
     */
    public function getHasher() {
        if (empty($this->hasher)) {
            $this->hasher = new Pbkdf2Hasher();
        }
        return $this->hasher;
    }
}
