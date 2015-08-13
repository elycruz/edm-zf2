<?php

namespace Edm\Service;

interface TermTaxonomyServiceAware {
    /**
     * Gets or sets the Term Taxonomy Service (overloaded method).
     * @return Edm\Service\AbstractCrudService
     */
    public function termTaxonomyService(Edm\Service\AbstractService $termTaxonomyService = null);
}
