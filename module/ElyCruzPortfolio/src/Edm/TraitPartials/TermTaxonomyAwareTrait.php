<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\TraitPartials;

/**
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
trait TermTaxonomyAwareTrait {

    public $termTaxService;

    public function getTermTaxService() {
        if (empty($this->termTaxService)) {
            $this->termTaxService = $this->getServiceLocator()
                    ->get('Edm\Service\TermTaxonomyService');
        }
        return $this->termTaxService;
    }

}
