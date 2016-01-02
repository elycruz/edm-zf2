<?php

namespace EdmTest\Form;

use EdmTest\Bootstrap,
    Edm\Form\AbstractForm,
    Edm\Form\UserForm;

/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 12:33 PM
 */
class UserFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array <[UserFrom]>
     */
    public function userFormProvider () {
        $userForm = new UserForm();
        $userForm->setServiceLocator(Bootstrap::getServiceManager());
        return [
            [$userForm]
        ];
    }

    /**
     * @return array <[UserForm, array<user, conact>>>
     */
    public function truthyFormDataProvider () {
        return [
            [
                $this->injectServiceLocatorIntoForm(new UserForm()), [
                    'user' => [
                        'screenName' => 'helloWorld',
                        'password' => 'helloworld',
                        'role' => 'user',
                        'accessGroup' => 'cms-manager',
                        'status' => 'activated'
                    ],
                    'contact' => [
                        'email' => 'hello@world.com',
                        'altEmail' => 'helloWorld@otherWorld.com'
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array <[UserForm, array<name, alias, user_group_alias>>
     */
    public function falsyFormDataProvider () {
        // Each one has one invalid field so we can expect
        // at least 1 error message in $form->getMessages()
        return [
            [
                $this->injectServiceLocatorIntoForm(new UserForm()), [
                'user' => [
                    'name' => 'Missing alias field',
                    'user_group_alias' => 'missing-field'
                ]
            ]],
            [
                $this->injectServiceLocatorIntoForm(new UserForm()), [
                'user' => [
                    'name' => 'No spaces allowed in `alias`.',
                    'alias' => 'no spaces allowed in alias'
                ]
            ]],
            [
                $this->injectServiceLocatorIntoForm(new UserForm()), [
                'user' => [
                    'alias' => 'missing-name-field',
                    'user_group_alias' => 'missing-field'
                ]
            ]],
            [
                $this->injectServiceLocatorIntoForm(new UserForm()), [
                'user' => [
                    'name' => '',
                    'alias' => 'invalid-name-property'
                ]
            ]]
        ];
    }

    /**
     * @dataProvider userFormProvider
     * @param UserForm $userForm
     */
    public function testExistence(UserForm $userForm) {
        $this->assertInstanceOf('Edm\Form\UserForm', $userForm);
    }

    /**
     * @dataProvider userFormProvider
     * @param UserForm $userForm
     */
    public function testHasExpectedFieldsets(UserForm $userForm) {
        $this->assertEquals(true, $userForm->has('user'));
        $this->assertEquals(true, $userForm->has('contact'));
        $this->assertEquals(true, $userForm->has('submit-and-reset'));
    }

    /**
     * @dataProvider userFormProvider
     * @param UserForm $userForm
     */
    public function testHasExpectedFieldsetTypes(UserForm $userForm) {
        $userFieldset = $userForm->get('user');
        $contactFieldset = $userForm->get('contact');
        $submitAndResetFieldset = $userForm->get('submit-and-reset');
        $this->assertInstanceOf('Edm\Form\Fieldset\UserFieldset', $userFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $userFieldset);
        $this->assertInstanceOf('Edm\Form\Fieldset\ContactFieldset', $contactFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $contactFieldset);
        $this->assertInstanceOf('Edm\Form\Fieldset\SubmitAndResetFieldset', $submitAndResetFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $submitAndResetFieldset);
    }

    /**
     * @dataProvider truthyFormDataProvider
     * @param UserForm $userForm
     * @param array $formData
     */
    public function testTruthyFormDataAfterValidation ($userForm, $formData) {
        $userKeys = [
            'role',
            'screenName',
            'password',
            'accessGroup',
            'status'
        ];
        
        $contactKeys = [
            'email',
            'altEmail'
        ];
        
        // Initialize form select element options
        $userForm->init();
        
        // Set data to validate
        $userForm->setData($formData);

        // Expect form data to be valid
        $this->assertTrue($userForm->isValid());

        // Get validated data
        $validatedData = $userForm->getData();

        // Expect form's `getData` method to return to
        // entries in an array ([user (fieldset), contact (fieldset), submit-and-reset (fieldset)])
        $this->assertCount(3, $validatedData);

        // Expect required fields to be set as data can't pass validation unless required fields are valid
        foreach ($userKeys as $key) {
            $this->assertEquals(true, isset($validatedData['user'][$key]));
        }

        // Expect required fields to be set as data can't pass validation unless required fields are valid
        foreach ($contactKeys as $key) {
            $this->assertEquals(true, isset($validatedData['contact'][$key]));
        }
    }

    /**
     * @dataProvider falsyFormDataProvider
     * @param UserForm $userForm
     * @param array $formData
     */
    public function testFalsyFormDataAfterValidation ($userForm, $formData) {
//        $userForm->setData($formData);
//        // Expect form data to be invalid
//        $this->assertFalse($userForm->isValid());
//        
//        // Get error messages
//        $userErrorMessages = $userForm->getMessages()['user'];
//        
//        // Get error message keys
//        $userErrorMessageKeys = array_keys($userErrorMessages);
//        
//        // Get possible keys for error messages
//        $possibleKeys = ['name', 'alias', 'user_group_alias'];
//        
//        // Check that error keys are in possible keys
//        foreach($userErrorMessageKeys as $key) {
//            $this->assertTrue(in_array($key, $possibleKeys));
//        }
    }

    public function injectServiceLocatorIntoForm (AbstractForm $form) {
        $form->setServiceLocator(Bootstrap::getServiceManager());
        return $form;
    }
    
}
