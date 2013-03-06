<?php

namespace Edm\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Edm\Db\DbDataHelperAccess,
    Edm\TraitPartials\ServiceLocatorAwareTrait,
    Edm\TraitPartials\DbDataHelperAccessTrait;

abstract class AbstractService implements ServiceLocatorAwareInterface,
 DbDataHelperAccess {
    use ServiceLocatorAwareTrait, DbDataHelperAccessTrait;
}