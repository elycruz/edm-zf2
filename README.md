edmzf2
===========================================
## DEPRECATED BRANCH!  FOR REFERENCE ONLY!
### superseded by new `master` branch

#### Note:  This branch was originally developed with out any testing put in place, no TDD or BDD, and was originally developed using zend framework 1.  The new version is developed using zend framework 2 and is following a TDD approach to developing the data/content management system.

Edm - The Extensible Data Management System

The system is written with Zend Framework 2 and mysql for it's database (see elycruz/edm-db-mysql).
Also the system will follow a TDD approach where we test first and write service and business process code later.

More documentation coming soon.

### Todos:

#### 0.3.0 MVP
- [ ] - Add tests:
    - [ ] - Tests for `Edm\Db`:
        - [ ] - Tests for `Edm\Db\DatabaseDataHelper`.
        - [ ] - Tests for `Edm\Db\TableGateway`.
        - [ ] - Tests for `Edm\Db\ResultSet\Proto`.
    - [X] - Tests for `Edm\InputFilter\DefaultInputOptions`.
- [ ] - Re-evaluate how 'aliases' are used in the EDM system.
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
