<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Service;

/**
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
trait TermTaxonomyServiceAwareTrait {

    public $termTaxService;

    public function getTermTaxService() {
        if (empty($this->termTaxService)) {
            $this->termTaxService = 
                $this->serviceLocator->get('Edm\Service\TermTaxonomyService');
        }
        return $this->termTaxService;
    }

}
