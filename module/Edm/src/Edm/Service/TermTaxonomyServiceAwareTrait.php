<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Service;

use Edm\Service\TermTaxonomyService;

/**
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
trait TermTaxonomyServiceAwareTrait {

    public $_termTaxService;

    public function termTaxonomyService(Edm\Service\TermTaxonomyService $service = null) {
        $isGetterCall = $service == null;
        if (empty($this->_termTaxService)) {
            $this->_termTaxService =
                $this->serviceLocator->get('Edm\Service\TermTaxonomyService');
        }
        return $this->_termTaxService;
    }

}
