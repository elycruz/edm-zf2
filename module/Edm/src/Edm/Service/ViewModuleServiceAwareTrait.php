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
    protected $viewModule;
    
    public function getViewModuleService() {
        return $this->viewModule;
    }

    public function setViewModuleService (AbstractService $viewModule) {
        $this->viewModule = $viewModule;
    }


    
}

