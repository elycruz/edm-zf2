<?
namespace Edm\Service;

use Edm\Service\AbstractService;

/*
 * To change this temp  late, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ViewModuleAwareTrait
 *
 * @author ElyDeLaCruz
 */
trait ViewModuleServiceAwareTrait {
    
    /**
     * View Module Service
     * @var Edm\Service\ViewModuleService
     */
    protected $viewModuleService;
    
    public function getViewModuleService() {
        if (empty($this->viewModuleService)) {
            $this->viewModuleService = $this->getServiceLocator()
                    ->get('Edm\Service\ViewModuleService');
        }
        return $this->viewModuleService;
    }
    
    public function setViewModuleService (AbstractService $viewModule) {
        $this->viewModuleService = $viewModule;
    }
    
}

