<?php
/**
 * @filename password_hasher/generate_hash.php
 * 
 * @desc A password_encoder for the default user password (superadmin)
 * Should be called from the command line `php password_encoder.php password` 
 * where password is the password you would like to use as the default user 
 * password.
 * 
 * @purpose To be used to reset/change the super admin password when access to 
 * user CRUD facilities made available by the application are not available.
 * 
 * @usage `php generate_hash.php <password_to_hash> <directory_to_put_hash_in>`
 * @param $password_to_hash - required
 * @param $directory_to_put_hash_in - optional;  If not supplied, writes hash 
 * and original string to the current directory.
 * 
 * @writes_file "_hashed_password.txt"
 * @writes_file "_un-hashed_password.txt"
 * 
 */

// Require our hasher
require(implode(array("..", "src", "CrackStation", "Pbkdf2_Hasher.php"), DIRECTORY_SEPARATOR));

// Make sure password isset
if (!isset($argv[1])) {
    echo "Please pass in a human readable password.";
    exit();
}

// Prefix path
$prefix_path = "";

// If argument at index argument 3 isset expect it to be the path.
if (isset($argv[2])) {
    $prefix_path = $argv[2];
    
    // Add path separator at the end if necessary
    if (strpos($prefix_path, DIRECTORY_SEPARATOR) !== count($prefix_path) - 1) {
        $prefix_path .= DIRECTORY_SEPARATOR;
    }
}

// Create a hasher
$hasher = new Pbkdf2_Hasher();

// File names to write
$hashed_password_file_name  =   $prefix_path . "_hashed_password.txt";
$unhashed_password_file_name =  $prefix_path . "_un-hashed_password.txt";

// Unhashed arg
$unhashed_key = $argv[1];

// Hash the password
$hashed_key = $hasher->create_hash($unhashed_key);

// Make sure password validates
echo $hasher->validate_against_hash($argv[1], $hashed_key);

// Put hashed password in text file.
file_put_contents($hashed_password_file_name, $hashed_key);

// Put unhashed password in another text file.
file_put_contents($unhashed_password_file_name, $argv[1]);

// Alert user of success
echo "\n---------------------------------------------\n";
echo "---------- Key generation success! ----------\n";
echo "---------------------------------------------\nn";
echo "Wrote \"" . $hashed_password_file_name . "\" and \n\"" 
        . $unhashed_password_file_name ."\".";
echo "\n\n---------------------------------------------\n";
echo "Generated hash:\n";
echo "---------------------------------------------\n\n";
echo $hashed_key;
echo "\n\n---------------------------------------------\n";
echo "Unhashed string:\n";
echo "---------------------------------------------\n\n";
echo $unhashed_key;
echo "\n\n---------------------------------------------\n";
echo "end of output.";
echo "\n---------------------------------------------\n\n";
echo "*** Note *** After using the generated files (entering them into db etc.) " .
        "delete them for safety purposes.";
