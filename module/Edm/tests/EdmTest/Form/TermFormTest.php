<?php

namespace EdmTest\Form;

use Edm\Form\TermForm;

/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 12:33 PM
 */
class TermFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array <[TermFrom]>
     */
    public function termFormProvider () {
        return [
            [new TermForm()]
        ];
    }

    /**
     * @return array <[TermForm, array<name, alias, term_group_alias>>
     */
    public function truthyFormDataProvider () {
        return [
            [
                new TermForm(), [
                'term' => [
                    'name' => 'Hello World',
                    'alias' => 'hello-world',
                    'term_group_alias' => 'hello-world'
                ]
            ]],
            [
                new TermForm(), [
                'term' => [
                    'name' => 'Some other value with no `term_group_alias`',
                    'alias' => 'some-other-value-with-no-term_group_alias'
                ]
            ]],
            [
                new TermForm(), [
                'term' => [
                    'name' => 'Short Name',
                    'alias' => 'short-name',
                    'term_group_alias' => 'hello'
                ]
            ]]
        ];
    }

    /**
     * @return array <[TermForm, array<name, alias, term_group_alias>>
     */
    public function falsyFormDataProvider () {
        // Each one has one invalid field so we can expect
        // at least 1 error message in $form->getMessages()
        return [
            [
                new TermForm(), [
                'term' => [
                    'name' => 'Missing alias field',
                    'term_group_alias' => 'missing-field'
                ]
            ]],
            [
                new TermForm(), [
                'term' => [
                    'name' => 'No spaces allowed in `alias`.',
                    'alias' => 'no spaces allowed in alias'
                ]
            ]],
            [
                new TermForm(), [
                'term' => [
                    'alias' => 'missing-name-field',
                    'term_group_alias' => 'missing-field'
                ]
            ]],
            [
                new TermForm(), [
                'term' => [
                    'name' => '',
                    'alias' => 'invalid-name-property'
                ]
            ]]
        ];
    }

    /**
     * @dataProvider termFormProvider
     * @param TermForm $termForm
     */
    public function testExistence(TermForm $termForm) {
        $this->assertInstanceOf('Edm\Form\TermForm', $termForm);
    }

    /**
     * @dataProvider termFormProvider
     * @param TermForm $termForm
     */
    public function testHasExpectedFieldsets(TermForm $termForm) {
        $this->assertEquals(true, $termForm->has('term'));
        $this->assertEquals(true, $termForm->has('submit-and-reset'));
    }

    /**
     * @dataProvider termFormProvider
     * @param TermForm $termForm
     */
    public function testHasExpectedFieldsetTypes(TermForm $termForm) {
        $termFieldset = $termForm->get('term');
        $submitAndResetFieldset = $termForm->get('submit-and-reset');
        $this->assertInstanceOf('Edm\Form\Fieldset\TermFieldset', $termFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $termFieldset);
        $this->assertInstanceOf('Edm\Form\Fieldset\SubmitAndResetFieldset', $submitAndResetFieldset);
        $this->assertInstanceOf('Zend\Form\Fieldset', $submitAndResetFieldset);
    }

    /**
     * @dataProvider truthyFormDataProvider
     * @param TermForm $termForm
     * @param array $formData
     */
    public function testTruthyFormDataAfterValidation ($termForm, $formData) {
        // Set data to validate
        $termForm->setData($formData);

        // Expect form data to be valid
        $this->assertEquals(true, $termForm->isValid());

        // Get validated data
        $validatedData = $termForm->getData();

        // Expect form's `getData` method to return to
        // entries in an array ([term (fieldset), submit-and-reset (fieldset)])
        $this->assertCount(2, $validatedData);

        // Expect required fields to be set as data can't pass validation unless required fields are valid
        foreach (['name', 'alias'] as $key) {
            $this->assertEquals(true, isset($validatedData['term'][$key]));
        }
    }

    /**
     * @dataProvider falsyFormDataProvider
     * @param TermForm $termForm
     * @param array $formData
     */
    public function testFalsyFormDataAfterValidation ($termForm, $formData) {
        $termForm->setData($formData);

        // Expect form data to be valid
        $this->assertEquals(false, $termForm->isValid());
        $termErrorMessages = $termForm->getMessages()['term'];
        $this->assertEquals(true,
            in_array(
                array_keys($termErrorMessages)[0],
                ['name', 'alias', 'term_group_alias']
            )
        );
    }

}
