<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edm_Form_Element_SelectSection
 *
 * @author ElyDeLaCruz
 */
class Edm_Form_Element_SelectSection extends
Zend_Form_Element_Select
{
    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formSelectSection';
}