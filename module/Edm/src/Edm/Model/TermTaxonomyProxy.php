<?php

namespace Edm\Model;

use Edm\Model\AbstractModel;

class TermTaxonomyProxy extends AbstractModel {

    /**
     * Valid Keys for Model
     * @var array
     */
    protected $validKeys = array(
        'term_taxonomy_id',
        'assocItemCount',
        'childCount'
    );

}