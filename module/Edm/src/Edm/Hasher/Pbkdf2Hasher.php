<?php

namespace Edm\Hasher;

/*
 * Password Hashing With PBKDF2 (http://crackstation.net/hashing-security.htm).
 * Copyright (c) 2013, Taylor Hornby
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, 
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation 
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * These constants may be changed without breaking existing hashes.
 * ** Note ** Maybe put these constants in a protected user directory (maybe
 * outside of current user directories where application is running (?); I.e.,
 * instead of /home/user/site/our_website/some_dir/pbkdf2_hasher_constants.php,
 * maybe use something like /home/protected_user/hasher_constants/our_website-hasher_constants.php).
 */
defined ("PBKDF2_HASH_ALGORITHIM") ||
    define("PBKDF2_HASH_ALGORITHM", "sha256");
defined ("PBKDF2_ITERATIONS") ||
    define("PBKDF2_ITERATIONS", 1000);
defined ("PBKDF2_SALT_BYTE_SIZE") ||
    define("PBKDF2_SALT_BYTE_SIZE", 24);
defined ("PBKDF2_HASH_BYTE_SIZE") ||
    define("PBKDF2_HASH_BYTE_SIZE", 24);
defined ("HASH_SECTION") ||
    define("HASH_SECTIONS", 4);
defined("HASH_ALGORITHIM_INDEX") ||
    define("HASH_ALGORITHM_INDEX", 0);
defined ("HASH_ITERATION_INDEX") ||
    define("HASH_ITERATION_INDEX", 1);
defined ("HASH_SALT_INDEX") ||
    define("HASH_SALT_INDEX", 2);
defined ("HASH_PBKDF2_INDEX") ||
    define("HASH_PBKDF2_INDEX", 3);

class Pbkdf2Hasher {
   
    public function __construct() {}
    
    /**
     * Creates a hash of string passed in the format: algorithm:iterations:salt:hash
     * @param string $un_hashed_str
     * @return string in format algorithm:iterations:salt:hash
     */
    public function create_hash($un_hashed_str)
    {
        // format: algorithm:iterations:salt:hash
        $salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
        return PBKDF2_HASH_ALGORITHM . ":" . PBKDF2_ITERATIONS . ":" .  $salt . ":" .
            base64_encode($this->pbkdf2(
                PBKDF2_HASH_ALGORITHM,
                $un_hashed_str,
                $salt,
                PBKDF2_ITERATIONS,
                PBKDF2_HASH_BYTE_SIZE,
                true
            ));
    }

    /**
     * Validates passed in un-hashed string against it's hashed counter part.
     * @param string $un_hashed_str
     * @param string $hashed_str
     * @return boolean
     */
    public function validate_against_hash($un_hashed_str, $hashed_str)
    {
        $params = explode(":", $hashed_str);
        if(count($params) < HASH_SECTIONS) {
           return false;
        }
        $pbkdf2 = base64_decode($params[HASH_PBKDF2_INDEX]);
        return $this->slow_equals(
            $pbkdf2,
            $this->pbkdf2(
                $params[HASH_ALGORITHM_INDEX],
                $un_hashed_str,
                $params[HASH_SALT_INDEX],
                (int)$params[HASH_ITERATION_INDEX],
                strlen($pbkdf2),
                true
            )
        );
    }

    /**
     * Compares two strings $a and $b in length-constant time.
     * @param string $a
     * @param string $b
     * @return boolean
     */
    public function slow_equals($a, $b)
    {
        $diff = strlen($a) ^ strlen($b);
        for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
        {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0;
    }

    /*
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     * $algorithm - The hash algorithm to use. Recommended: SHA256
     * $un_hashed_str - The password.
     * $salt - A salt that is unique to the password.
     * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
     * $key_length - The length of the derived key in bytes.
     * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
     * Returns: A $key_length-byte key derived from the password and salt.
     *
     * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
     *
     * This implementation of PBKDF2 was originally created by https://defuse.ca
     * With improvements by http://www.variations-of-shadow.com
     * 
     * @param string  $algorithm
     * @param string  $un_hashed_str
     * @param string  $salt
     * @param {int}     $count
     * @param {int}     $key_length
     * @param {boolean} $raw_output
     * @return string
     */
    public function pbkdf2($algorithm, $un_hashed_str, $salt, $count, $key_length, $raw_output = false)
    {
        $algorithm = strtolower($algorithm);
        if(!in_array($algorithm, hash_algos(), true)) {
            trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
        }
        if($count <= 0 || $key_length <= 0) {
            trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
        }

        if (function_exists("hash_pbkdf2")) {
            // The output length is in NIBBLES (4-bits) if $raw_output is false!
            if (!$raw_output) {
                $key_length = $key_length * 2;
            }
            return hash_pbkdf2($algorithm, $un_hashed_str, $salt, $count, $key_length, $raw_output);
        }

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $un_hashed_str, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $un_hashed_str, true));
            }
            $output .= $xorsum;
        }

        if($raw_output) {
            return substr($output, 0, $key_length);
        }
        else {
            return bin2hex(substr($output, 0, $key_length));
        }
    }
}
