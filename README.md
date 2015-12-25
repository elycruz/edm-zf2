edmzf2
===========================================
Edm - Extensible Data Management System, is a system written with
Zend Framework 2, Mysql 5.6+, and Php 7.0+.  The system is written with 
TDD approach and is designed to be developer centric.
The system is currently in an alpha-refactor stage and will have it's upcoming 
features and functionality all documented in this repo (for now).

### Requirements
- Zend Framework 2+
- Php 7.0+
- Mysql 5.6+
- Installed database from github repo 'elycruz/edm-db-mysql'.

### Caveats
- All development must be performed in TDD format.

#### Code rules:
- No redeclaring of variables in same scope.
- No methods or functions with more than 89 line length.
- No classes with line length greater than 610.
- Characters per line 89-144.

### Usage notes:

The following defines have to be defined in one of your autoload files
(preferably in one of your *local.php files):

```
<?php

// Edm salt seed
defined('EDM_SALT') ||
        define('EDM_SALT', 'salt-goes-here');

// Edm token seed
defined('EDM_TOKEN_SEED') ||
        define('EDM_TOKEN_SEED', 'token-seed-goes-here');

/**
 * ** Note ** Maybe put this config in an outer directory (outside of the application). I.e.,
 * instead of using '/home/user/www/some-website/configs/local.php',
 * use something like the following instead 
 * '/home/protected_user/www/some-website/configs/local.php'.
 */
define("EDM_PBKDF2_ALGORITIHM", "sha256");
define("EDM_PBKDF2_ITERATIONS", 1597);
define("EDM_PBKDF2_SALT_BYTE_SIZE", 21);
define("EDM_PBKDF2_HASH_BYTE_SIZE", 21);
define("EDM_PBKDF2_SECTIONS", 4);
define("EDM_PBKDF2_SECTIONS_VERSION", 1);
define("EDM_PBKDF2_ALGORITHM_INDEX", 0);
define("EDM_PBKDF2_ITERATIONS_INDEX", 1);
define("EDM_PBKDF2_SALT_INDEX", 2);
define("EDM_PBKDF2_HASH_INDEX", 3);

// Return our '*local.php' config
return [
    'edm-db' => [
        'dbname' => 'db-name-here',
        'host' => 'db-host-ip/url-here',
        'username' => 'db-user-here',
        'password' => 'db-password-here'
    ],

    // Not used yet but will be used in a later version
    'edm-user-service' => [
        // Pbkdf2Hasher
        'hasher' => [
            // Hasher parameters
            'hashAlgorithm' => EDM_PBKDF2_ALGORITIHM,
            'numIterations' => EDM_PBKDF2_ITERATIONS,
            'saltByteSize' => EDM_PBKDF2_HASH_BYTE_SIZE,
            'hashByteSize' => EDM_PBKDF2_SALT_BYTE_SIZE,
            'hashSections' => EDM_PBKDF2_SECTIONS,
            // Keep track of hasher constants` changes here
            'hashSectionsTemplateVersion' => EDM_PBKDF2_SECTIONS_VERSION,
            'hashSectionVersionTemplateMap' => [
                1 => 'algorithm:iterations:salt:hash'
            ],
            // Indexes of pertinent parts of the hasher
            'algorithmIndex' => EDM_PBKDF2_ALGORITHM_INDEX,
            'iterationsIndex' => EDM_PBKDF2_ITERATIONS_INDEX,
            'saltIndex' => EDM_PBKDF2_SALT_INDEX,
            'hashIndex' => EDM_PBKDF2_HASH_INDEX,
        ]
    ]
];

```

### Todos:

#### 0.3.0 MVP
- [ ] - Add tests:
    - [ ] - `Edm\Db`:
        - [X] - ~~`Edm\Db\DatabaseDataHelper`~~ `Edm\Db\DbDataHelper`.
        - [ ] - `Edm\Db\ResultSet\Proto`:
            - [ ] - `Edm\Db\ResultSet\Proto\AbstractProto`. (reinstantiating 
                this as not complete because `AbstractProto` should have it's
                own tests and not have it's tests mixed in with other classes' 
                tests.
            - [X] - `Edm\Db\ResultSet\Proto\ContactProto`.
            - [X] - `Edm\Db\ResultSet\Proto\ContactUserRelProto`.
            - [X] - `Edm\Db\ResultSet\Proto\DateInfoProto`.
            - [X] - `Edm\Db\ResultSet\Proto\PostProto`.
            - [X] - `Edm\Db\ResultSet\Proto\ProtoInterface`.
            - [X] - `Edm\Db\ResultSet\Proto\TermProto`.
            - [X] - `Edm\Db\ResultSet\Proto\TermTaxonomyProto`.
            - [X] - `Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto`.
            - [X] - `Edm\Db\ResultSet\Proto\UserProto`.
        - [ ] - `Edm\Db\TableGateway`:
            - [ ] - `Edm\Db\TableGateway\BaseTableGateway`.
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
            - [X] - `Edm\Form\Fieldset\UserFieldset`.
        - [ ] - `Edm\Form\TermFormTest`.
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
