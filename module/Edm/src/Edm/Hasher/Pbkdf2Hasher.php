<?php
declare(strict_types=1);
        
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
//defined ("PBKDF2_HASH_ALGORITHIM") || define ("PBKDF2_HASH_ALGORITHM", "sha256");
//defined ("PBKDF2_ITERATIONS")      || define ("PBKDF2_ITERATIONS",     1000);
//defined ("PBKDF2_SALT_BYTE_SIZE")  || define ("PBKDF2_SALT_BYTE_SIZE", 24);
//defined ("PBKDF2_HASH_BYTE_SIZE")  || define ("PBKDF2_HASH_BYTE_SIZE", 24);
//defined ("HASH_SECTION")           || define ("HASH_SECTIONS",         4);
//defined ("HASH_ALGORITHIM_INDEX")  || define ("HASH_ALGORITHM_INDEX",  0);
//defined ("HASH_ITERATION_INDEX")   || define ("HASH_ITERATION_INDEX",  1);
//defined ("HASH_SALT_INDEX")        || define ("HASH_SALT_INDEX",       2);
//defined ("HASH_PBKDF2_INDEX")      || define ("HASH_PBKDF2_INDEX",     3);

// @todo don't forget to cleanup this class (remove setters we don't want used 
// more than once).

class Pbkdf2Hasher {
    
    /**
     * Hash algorithm to use.
     * @var string
     */
    protected $_hashAlgorithm = 'sha256';
    
    /**
     * Salt byte size.  
     * @recommendation: make the value the same as `$_hashByteSize` 
     *  (harder for attackers to differentiate between hash and salt values).
     * @var string
     */
    protected $_saltByteSize = 21;
    
    /**
     * Pbkdf2 hash byte size.
     * @recommendation: make the value the same as `$_saltByteSize` 
     *  (harder for attackers to differentiate between hash and salt values).
     * @var string
     */
    protected $_hashByteSize = 21;
    
    /**
     * @var int
     */
    protected $_numIterations = 1597;
    
    /**
     * @var int
     */
    protected $_numSections = 4;
    
    /**
     * @var int
     */
    protected $_algorithmIndex = 0;
    
    /**
     * @var int
     */
    protected $_iterationsIndex = 1;
    
    /**
     * @var int
     */
    protected $_saltIndex = 2;
    
    /**
     * @var int
     */
    protected $_hashIndex = 3;
    
    /**
     * @var int
     */
    protected $_hashSectionsTemplateVersion = 1;
    
    /**
     * @var array
     */
    protected $_hashSectionsVersionTemplateMap = [];
    
    public function __construct(array $options = null) {
        if (!isset($options)) {
            return;
        }
        // Auto populate via setters
        foreach ($options as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
            else {
                
            }
        }
    }
    
    /**
     * Creates a hash of string passed in the format: algorithm:iterations:salt:hash
     * @param string $un_hashed_str
     * @return string in format algorithm:iterations:salt:hash
     */
    public function create_hash($un_hashed_str)
    {
        $iterations = $this->_numIterations;
        $hashAlgorithm = $this->_hashAlgorithm;
        $saltByteSize = $this->_saltByteSize;
        $hashByteSize = $this->_hashByteSize;
        
        $salt = base64_encode(mcrypt_create_iv($saltByteSize, MCRYPT_DEV_URANDOM));
        $hash = base64_encode($this->pbkdf2(
                $hashAlgorithm,
                $un_hashed_str,
                $salt,
                $iterations,
                $hashByteSize,
                true
            ));
        
        // Prepare to order sections
        $out = [];
        $out[$this->_hashIndex] = $hash;
        $out[$this->_saltIndex] = $salt;
        $out[$this->_iterationsIndex] = $iterations;
        $out[$this->_algorithmIndex] = $hashAlgorithm;
        
        // Sort indices
        ksort($out);
        
        // format: algorithm:iterations:salt:hash
        return implode( ':', $out);
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
        if(count($params) < $this->_numSections) {
           return false;
        }
        $pbkdf2 = base64_decode($params[$this->_hashIndex]);
        return $this->slow_equals(
            $pbkdf2,
            $this->pbkdf2(
                $params[$this->_algorithmIndex],
                $un_hashed_str,
                $params[$this->_saltIndex],
                (int)$params[$this->_iterationsIndex],
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
    
    public function getHashAlgorithm() {
        return $this->_hashAlgorithm;
    }

    public function getSaltByteSize() {
        return $this->_saltByteSize;
    }

    public function getHashByteSize() {
        return $this->_hashByteSize;
    }

    public function getNumIterations() {
        return $this->_numIterations;
    }

    public function getNumSections() {
        return $this->_numSections;
    }

    public function getAlgorithmIndex() {
        return $this->_algorithmIndex;
    }

    public function getSaltIndex() {
        return $this->_saltIndex;
    }

    public function getHashIndex() {
        return $this->_hashIndex;
    }

    public function setHashAlgorithm(string $hashAlgorithm) {
        $this->_hashAlgorithm = $hashAlgorithm;
        return $this;
    }

    public function setSaltByteSize(int $saltByteSize) {
        $this->_saltByteSize = $saltByteSize;
        return $this;
    }

    public function setHashByteSize(int $hashByteSize) {
        $this->_hashByteSize = $hashByteSize;
        return $this;
    }

    public function setNumIterations(int $numIterations) {
        $this->_numIterations = $numIterations;
        return $this;
    }

    public function setNumSections(int $numSections) {
        $this->_numSections = $numSections;
        return $this;
    }

    public function setAlgorithmIndex(int $algorithmIndex) {
        $this->_algorithmIndex = $algorithmIndex;
        return $this;
    }

    public function setSaltIndex(int $saltIndex) {
        $this->_saltIndex = $saltIndex;
        return $this;
    }

    public function setHashIndex(int $hashIndex) {
        $this->_hashIndex = $hashIndex;
        return $this;
    }
    
    public function getIterationsIndex() {
        return $this->_iterationsIndex;
    }

    public function getHashSectionsTemplateVersion() {
        return $this->_hashSectionsTemplateVersion;
    }

    public function getHashSectionsVersionTemplateMap() {
        return $this->_hashSectionsVersionTemplateMap;
    }

    public function setIterationsIndex($iterationsIndex) {
        $this->_iterationsIndex = $iterationsIndex;
        return $this;
    }

    public function setHashSectionsTemplateVersion($hashSectionsTemplateVersion) {
        $this->_hashSectionsTemplateVersion = $hashSectionsTemplateVersion;
        return $this;
    }

    public function setHashSectionsVersionTemplateMap($hashSectionsVersionTemplateMap) {
        $this->_hashSectionsVersionTemplateMap = $hashSectionsVersionTemplateMap;
        return $this;
    }

}