<?php

namespace Edm\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Edm\Db\DbAware,
    Edm\Db\DbDataHelperAware,
    Edm\TraitPartials\DbAwareTrait,
    Edm\TraitPartials\ServiceLocatorAwareTrait,
    Edm\TraitPartials\DbDataHelperAwareTrait;

abstract class AbstractService implements ServiceLocatorAwareInterface,
 DbDataHelperAware, DbAware {
    use ServiceLocatorAwareTrait, DbDataHelperAwareTrait, DbAwareTrait;
}