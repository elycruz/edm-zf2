<?php

namespace Edm\Model;

use Edm\Model\AbstractModel;

class TermTaxonomyProxy extends AbstractModel {

    protected $inputFilter = null;
    
    protected $validKeys = array(
        'term_taxonomy_id',
        'assocItemCount',
        'childCount'
    );

    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }
}