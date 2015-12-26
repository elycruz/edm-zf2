<?php

namespace Edm\Service;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Edm\Service\AbstractCrudService,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Db\TableGateway\PostTable,
    Edm\Db\TableGateway\PostCategoryRelTable,
    Edm\Db\TableGateway\MediaTable,
    Edm\Filter\Slug,
    Edm\UserAware,
    Edm\UserAwareTrait;

class PostMediaService extends AbstractCrudService {
    
}

