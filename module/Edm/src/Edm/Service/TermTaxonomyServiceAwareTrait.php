<?php

namespace Edm\Service;

/**
 * Gives the implementing class/trait a getter and setter for the term taxonomy service.
 * @note Assumes service locator aware interface.
 * @requires Zend\ServiceManager\ServiceLocatorAwareInterface
 * @author ElyDeLaCruz
 */
trait TermTaxonomyServiceAwareTrait {

    /**
     * Term Taxonomy Service holder.
     * @var \Edm\Service\TermTaxonomyService
     */
    protected $_termTaxonomyService;

    /**
     * Service locator storage key for term taxonomy service.
     * @var string
     */
    protected $_termTaxonomyService_serviceLocatorKey = 'Edm\Service\TermTaxonomyService';

    /**
     * @return TermTaxonomyService
     */
    public function getTermTaxonomyService()
    {
        // If term tax service is empty
        if (empty($this->_termTaxonomyService)) {
            $serviceLocator = $this->getServiceLocator();

            // Fetch term tax service from service locator if it has it
            if (!$serviceLocator->has($this->_termTaxonomyService_serviceLocatorKey)) {
                $this->_termTaxonomyService = new TermTaxonomyService($serviceLocator);
                $serviceLocator->setService($this->_termTaxonomyService_serviceLocatorKey,
                    $this->_termTaxonomyService);
            }
            // Else get it from service locator
            else {
                $this->_termTaxonomyService = $serviceLocator
                    ->get($this->_termTaxonomyService_serviceLocatorKey);
            }
        }

        return $this->_termTaxonomyService;
    }

    /**
     * @param AbstractCrudService $termTaxonomyService
     * @return TermTaxonomyServiceAwareTrait
     */
    public function setTermTaxonomyService(AbstractCrudService $termTaxonomyService)
    {
        $this->_termTaxonomyService = $termTaxonomyService;
        return $this;
    }

}
