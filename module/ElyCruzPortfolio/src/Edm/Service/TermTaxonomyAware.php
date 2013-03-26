<?php

namespace Edm\Service;

interface TermTaxonomyAware {
    /**
     * Gets the Term Taxonomy Service
     * @return Edm\Service\AbstractCrudService
     */
    public function getTermTaxService();
}
