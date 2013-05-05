<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace EdmAccessGateway\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Description of Controller
 *
 * @author ElyDeLaCruz
 */
class ErrorController extends AbstractActionController {
    
    public function notAuthorizedAction () {
        return array();
    }
    
    public function resourceNotFoundAction () {
        return array();
    }
    
}
