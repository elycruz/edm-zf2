edmzf2
===========================================
Edm - The Extensible Data Management System

The system is written with Zend Framework 2 and mysql for it's database (see elycruz/edm-db-mysql).
Also the system will follow a TDD approach where we test first and write service and business process code later.

More documentation coming soon.

### Usage notes:

The following defines have to be defined in one of your autoload files
(preferably in one of your *.local.php files):

```
<?php

// Application path
// 'APP_PATH' is already defined in global.php
// defined('APP_PATH') ||
// define('APP_PATH', realpath(__DIR__ . '/../../'));

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

### Code rules:
- No redeclaring of variables in same scope.
- No methods or functions with more than 89 line length.
- No classes with line length greater than 610.
- Chars per line 89-144.

### Todos:

#### 0.3.0 MVP
- [ ] - Add tests:
    - [ ] - `Edm\Db`:
        - [X] - ~~`Edm\Db\DatabaseDataHelper`~~ `Edm\Db\DbDataHelper`.
        - [ ] - `Edm\Db\ResultSet\Proto`:
            - [X] - `Edm\Db\ResultSet\Proto\AbstractProto`.
            - [X] - `Edm\Db\ResultSet\Proto\ContactProto`.
            - [X] - `Edm\Db\ResultSet\Proto\ContactUserRelProto`.
            - [X] - `Edm\Db\ResultSet\Proto\DateInfoProto`.
            - [X] - `Edm\Db\ResultSet\Proto\ProtoInterface`.
            - [X] - `Edm\Db\ResultSet\Proto\TermProto`.
            - [X] - `Edm\Db\ResultSet\Proto\TermTaxonomyProto`.
            - [X] - `Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto`.
            - [X] - `Edm\Db\ResultSet\Proto\UserProto`.
        - [ ] - `Edm\Db\TableGateway`:
            - [X] - `Edm\Db\TableGateway\ContactTable`.
            - [X] - `Edm\Db\TableGateway\ContactUserRelTable`.
            - [X] - `Edm\Db\TableGateway\DateInfoTable`.
            - [X] - `Edm\Db\TableGateway\TermTable`.
            - [X] - `Edm\Db\TableGateway\TermTaxonomyTable`.
            - [X] - `Edm\Db\TableGateway\TermTaxonomyProxyTable`.
            - [X] - `Edm\Db\TableGateway\UserTable`.
    - [X] - `Edm\Filter`:
        - [X] - `Edm\Filter\Alias`.
        - [X] - `Edm\Filter\Slug`.
    - [ ] - `Edm\Form`:
        - [ ] - `Edm\Form\Fieldset`:
            - [X] - `Edm\Form\Fieldset\SubmitAndResetFieldset`.
            - [X] - `Edm\Form\Fieldset\TermFieldset`.
        - [ ] - `Edm\Form\TermForm`.
    - [X] - `Edm\Hasher\Pbkdf2Hasher`.
    - [X] - `Edm\InputFilter\DefaultInputOptions`.
    - [X] - ~~`Edm\InputFilter\DefaultInputOptionsAware`.~~  The tests above cover this item.
    - [ ] - `Edm\Service`:
        - [X] - Preliminary `Edm\Service\TermTaxonomyService` tests.
        - [X] - `Edm\Service\UserService`
            - [ ] - Solve the `Pbkdf2Hasher->create_hash` output length mystery 
                (we need to know the strings exact length with and without
                 padding even if it means abstracting away the last '=' or '=='
                 that `base64_encode` adds depending on how much padding the last
                character has).
        - [ ] - `Edm\Service\PostService`.
        - [ ] - `Edm\Service\PostCommentService`.
        - [ ] - `Edm\Service\PostGalleryService`.
        - [ ] - `Edm\Service\PostMediaService`.
        - [ ] - `Edm\Service\PostFlaggingService`.
        - [ ] - Upgrade all `*\Service\*` tests to use `Edm\Db\ResultSet\Proto\*` classes
         as their data objects.
        
- [X] - Re-evaluate how 'aliases' are used in the EDM system.
    Resources:
        - valid url characters stackoverflow: (http://stackoverflow.com/questions/4669692/valid-characters-for-directory-part-of-a-url-for-short-links)
    Findings:
        - Slugs are the same as object aliases (dash separated words with no punctuation).
        - Slugs can be used the same way we use aliases in our edm application.
        - Slug/alias (https://en.wikipedia.org/wiki/Semantic_URL#Slug)
        - Slug (https://en.wikipedia.org/wiki/Slug_(publishing))
- [ ] - Add UML diagrams of system.
- [ ] - Add UML diagrams of the database (edm-db-mysql) as it stands.
- [ ] - Add RDBMS as git sub-module to the project.

#### Other:
 - [ ] - Look into using `Expressive`

#### Other tasks
 - [ ] List all forms in module.config.php so that we can take advantage of
    the service manager's features.

#### 1.0.0 MVP
- [ ] - Add routes management from the cms
- [ ] - Add module management from the cms
- [ ] - Add controller management from the cms
- [ ] - Add action management from the cms
- [ ] - Maybe implement the aforementioned via something like the following in
the ui:
    - refresh controllers list
    - refresh actions list
    - refresh routes list

##### Ideas for aforementioned:
- [ ] - Maybe add an extra directory for loading `Edm` "generated" configs.
- [ ] - Generate, backup, and re-write configs (routes, module config, controller, and action configs from edm or other module);
- [ ] - Maybe do introspection of modules, controller directories and the like to populate cms page for managing routes module

more to come as development continues.
