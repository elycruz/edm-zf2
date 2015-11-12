edmzf2
===========================================
Edm - Extensible Data Management System.

The system is written with Zend Framework 2 and mysql for it's database (see elycruz/edm-db-mysql).

More documentation comming soon.

### Todos:

#### 0.3.0 MVP
 - [ ] - Finish all tests for `Edm\Db\TableGateway\*`.
 - [ ] - Finish all tests for `Edm\Db\ResultSet\Proto\*`.

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
