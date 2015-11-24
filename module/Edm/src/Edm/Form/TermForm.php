<?php

namespace Edm\Form;

use Edm\Form\Fieldset\SubmitAndResetFieldset,
    Edm\Form\Fieldset\TermFieldset;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class TermForm extends AbstractForm {

    public function __construct() {

        parent::__construct('term-form');

        $this->setAttribute('method', 'post');

        $this->add(new TermFieldset('term'));

        $this->add(new SubmitAndResetFieldset('submit-and-reset'));

    }

}
