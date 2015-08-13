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

    public $_termTaxService;

    public function termTaxonomyService() {
        if (empty($this->_termTaxService)) {
            $this->_termTaxService =
                $this->serviceLocator->get('Edm\Service\TermTaxonomyService');
        }
        return $this->_termTaxService;
    }

}
