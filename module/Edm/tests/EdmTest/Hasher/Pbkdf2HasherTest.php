<?php

/**
 * @note We should probably store the different 
 */
namespace EdmTest\Service;

use Edm\Hasher\Pbkdf2Hasher;

// REFERENCE VALUES
//defined ("PBKDF2_HASH_ALGORITHIM") || define ("PBKDF2_HASH_ALGORITHM", "sha256");
//defined ("PBKDF2_ITERATIONS")      || define ("PBKDF2_ITERATIONS",     1000);
//defined ("PBKDF2_SALT_BYTE_SIZE")  || define ("PBKDF2_SALT_BYTE_SIZE", 24);
//defined ("PBKDF2_HASH_BYTE_SIZE")  || define ("PBKDF2_HASH_BYTE_SIZE", 24);
//defined ("HASH_SECTION")           || define ("HASH_SECTIONS",         4);
//defined ("HASH_ALGORITHIM_INDEX")  || define ("HASH_ALGORITHM_INDEX",  0);
//defined ("HASH_ITERATION_INDEX")   || define ("HASH_ITERATION_INDEX",  1);
//defined ("HASH_SALT_INDEX")        || define ("HASH_SALT_INDEX",       2);
//defined ("HASH_PBKDF2_INDEX")      || define ("HASH_PBKDF2_INDEX",     3);

/**
 * Description of Pbkdf2HasherTest
 * @author Ely
 */
class Pbkdf2HasherTest extends \PHPUnit_Framework_TestCase {
    
    public function hasherOptionsProvider () {
        return [
            [[
                'hashAlgorithm' => 'sha256',
                'iterations' => 1000,
                'saltByteSize' => 21,
                'hashByteSize' => 21,
                'hashSections' => 4,
                'algorithmIndex' => 0,
                'iterationIndex' => 1,
                'saltIndex' => 2,
                'hashIndex' => 3,
            ], 'unhashed-string' ],
            [[
                'hashAlgorithm' => 'sha256',
                'iterations' => 89,
                'saltByteSize' => 13,
                'hashByteSize' => 13,
                'hashSections' => 4,
                'algorithmIndex' => 1,
                'iterationIndex' => 0,
                'saltIndex' => 3,
                'hashIndex' => 2,
            ], 'other-unhashed-str' ],
            [[
                'hashAlgorithm' => 'sha256',
                'iterations' => 89,
                'saltByteSize' => 8,
                'hashByteSize' => 13,
                'hashSections' => 4,
                'algorithmIndex' => 0,
                'iterationIndex' => 1,
                'saltIndex' => 2,
                'hashIndex' => 3,
            ], 'other-crazy-unhashed-str-(&^*)sally-sells-sea-shells-down-'
                . 'by-the-sea-shore-99!#*$' ]
        ];
    }
    
    /**
     * @dataProvider hasherOptionsProvider
     * @param array $options
     * @param string $unhashed_str
     */
    public function test_create_hash ($options, $unhashed_str) {
        $hasher = new Pbkdf2Hasher($options);
        $hashed_str = $hasher->create_hash($unhashed_str);
        $this->assertEquals(true, $hasher->validate_against_hash($unhashed_str, $hashed_str));
    }
}
