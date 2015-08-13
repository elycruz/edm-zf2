<?php

namespace Edm\Service;

interface TermTaxonomyServiceAware {
    /**
     * Gets the Term Taxonomy Service
     * @return Edm\Service\AbstractCrudService
     */
    public function termTaxonomyService();
}
