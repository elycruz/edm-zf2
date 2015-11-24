<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 4:31 PM
 */

namespace EdmTest\Form\Fieldset;

use Edm\Form\Fieldset\SubmitAndResetFieldset;

class SubmitAndResetFieldsetTest extends \PHPUnit_Framework_TestCase
{
    public function testExistence () {
        $fieldset = new SubmitAndResetFieldset();
        $this->assertInstanceOf('Edm\Form\Fieldset\SubmitAndResetFieldset', $fieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $fieldset);
    }

    public function testHasExpectedFields () {
        $fieldset = new SubmitAndResetFieldset();
        $this->assertEquals($fieldset->has('reset'), true);
        $this->assertEquals($fieldset->has('submit'), true);
        $resetElm = $fieldset->get('reset');
        $submitElm = $fieldset->get('submit');
        $this->assertInstanceOf('Zend\Form\Element', $resetElm);
        $this->assertInstanceOf('Zend\Form\Element', $submitElm);
    }
}
