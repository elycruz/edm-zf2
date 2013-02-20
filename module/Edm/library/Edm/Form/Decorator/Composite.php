<?php

/**
 * My test custom decorator for forms
 */
class Edm_Form_Decorator_Composite extends Zend_Form_Decorator_Abstract {

    public function buildLabel() {
        $elm = $this->getElement();
        $lab = '';
        if ($elm->getLabel()) {
            $lab .= $elm->getLabel() . ':';
            $lab = $elm->getView()->formLabel(
                            $elm->getName(), $lab
                    ) . '<br />';

            if ($elm->isRequired()) {
                $lab = '<span class="red">*</span>' . $lab;
            }
            return $lab;
        }

        return '';
    }

    public function buildInput() {
        $elm = $this->getElement();
        $hel = $elm->helper;

        return $elm->getView()->$hel(
                        $elm->getName(), $elm->getValue(), $elm->getAttribs(), $elm->options
        );
    }

    public function buildDescription() {
        $elm = $this->getElement();
        $desc = $elm->getDescription();

        if (empty($desc)) {
            return '';
        }

        return '<div class="description tsml">' .
                $desc . '</div>';
    }

    public function buildErrors() {
        $element = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<div class="errors">' .
                $element->getView()->formErrors($messages) . '</div>';
    }

    public function render($content) {
        $elm = $this->getElement();

        if (!$elm instanceOf Zend_Form_Element) {
            return $content;
        }

        if ($elm->getView() === Null) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $this->buildLabel();
        $input = $this->buildInput();
        $errors = $this->buildErrors();
        $desc = $this->buildDescription();

        $output = '<div class="form-item">'
                . $label
                . $input
                . $desc
                . $errors
                . '</div>'
        ;
        switch ($placement) {
            case ( self::PREPEND ) :
                return $output . $separator . $content;
            case ( self::APPEND ) :
            default :
                return $content . $separator . $output;
                break;
        }
    }

}