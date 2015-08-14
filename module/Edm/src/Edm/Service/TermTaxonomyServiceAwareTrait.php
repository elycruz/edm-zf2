<?php

namespace Edm\Service;

use Edm\Service\TermTaxonomyService;

/**
 * Gives the implementing class/trait an overloaded getter/setter function
 * for fetching the term taxonomy service using the service locator, initially.
 * @see @method `termTaxonomyService` in definition.
 * @note Assumes service locator aware interface.
 * @requires ServiceLocatorAware
 * @author ElyDeLaCruz
 */
trait TermTaxonomyServiceAwareTrait {

    /**
     * Term Taxonomy Service holder.
     * @var Edm\Service\TermTaxonomyService
     */
    protected $_termTaxonomyService;

    /**
     * Service locator storage key for term taxonomy service.
     * @var string
     */
    protected $_termTaxonomyService_serviceLocatorKey = 'edm-term-taxonomy-service';

    /**
     * Overloaded term taxonomy service getter and setter.  Returns the term taxonomy service
     * from the service locator if it is set or creates a new one and sets it on the
     * service locator.  Uses key to store and fetch the service from the locator
     * @param Edm\Service\AbstractService $termTaxonomyService
     * @return TermTaxonomyService|TermTaxonomyServiceAwareTrait
     */
    public function termTaxonomyService(Edm\Service\AbstractService $termTaxonomyService = null) {
        $isGetterCall = $termTaxonomyService == null;
        $retVal = $this;

        // Is getter call?
        if ($isGetterCall) {

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

            // Set term tax service as return value
            $retVal = $this->_termTaxonomyService;
        }

        // Else is setter call.  Set term tax service to passed in value.
        else {
            $this->_termTaxonomyService = $termTaxonomyService;
        }

        // Return
        return $retVal;
    }

}
