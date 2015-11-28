Sample, Index module for use with the ZF2 MVC layer.

README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.
```
<VirtualHost *:80>
   DocumentRoot "C:/Workspace/your-app/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "H:/Workspace/your-app/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>
```

### Usage notes:

The following defines have to be defined in one of your autoload files
(preferably in one of your *.local.php files):

```
<?php

// Application path
defined('APP_PATH') ||
define('APP_PATH', realpath(__DIR__ . '/../../'));

// Edm salt seed
defined('EDM_SALT') ||
define('EDM_SALT', 'somesalt');

// Edm pepper seed
defined('EDM_PEPPER') ||
define('EDM_PEPPER', 'somepepper');

// Edm token seed
defined('EDM_TOKEN_SEED') ||
define('EDM_TOKEN_SEED', 'sometokenseed');

/**
 * These constants may be changed without breaking existing hashes.
 * ** Note ** Maybe put these constants in a protected user directory (maybe
 * outside of current user directories where application is running (?); I.e.,
 * instead of /home/user/site/our_website/some_dir/pbkdf2_hasher_constants.php,
 * maybe use something like /home/protected_user/hasher_constants/our_website-hasher_constants.php).
 */
// Note these are hashes from the initial port of the pbkdf2 code from crackstation.com
// This will be refactored and cleaned up to maybe use a configuration file instead.
define("PBKDF2_HASH_ALGORITHM", "sha256");
define("PBKDF2_ITERATIONS", 1000);
define("PBKDF2_SALT_BYTE_SIZE", 24);
define("PBKDF2_HASH_BYTE_SIZE", 24);
define("HASH_SECTIONS", 4);
define("HASH_ALGORITHM_INDEX", 0);
define("HASH_ITERATION_INDEX", 1);
define("HASH_SALT_INDEX", 2);
define("HASH_PBKDF2_INDEX", 3);

```
