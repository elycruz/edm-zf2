<?php
/**
 * Description of EdmForm
 * @author ElyDeLaCruz
 */
class Edm_Form_Form extends Zend_Form
{
    /**
     * Allowed tags for description fields
     * @var array
     */
    protected $_allowed_tags = array(
        'div', 'span', 'object', 'h1', 'h2', 'h3', 'h4',
        'h5', 'h6', 'hr', 'p', 'blockquote', 'pre', 'a', 'abbr', 'acronym',
        'address', 'big', 'cite', 'code', 'del', 'dfn', 'em',
        'img', 'ins', 'q', 's', 'samp', 'small', 'strong', 'sub',
        'sup', 'tt', 'var', 'b', 'i', 'dl', 'dt', 'dd', 'ol', 'ul', 'li',
        'fieldset', 'label', 'legend', 'table', 'caption', 'tbody',
        'tfoot', 'thead', 'tr', 'th', 'td'
    );

    /**
     * Allowed attribs
     * @var array
     */
    protected $_allowed_attribs = array(
        'style', 'title', 'src', 'id', 'class'
    );

    /**
     * A collection of commonly used form field validators and filters for
     * fields used within our application
     * @var array $_validators
     */
    protected $_validators_and_filters;

    /**
     * Constructor
     * @param <type> $options
     */
    public function  __construct($options = null)
    {
        $this->_validators_and_filters = array(
            'alias' => array(
                'validators' => array(
                    new Zend_Validate_StringLength(array('min' => 2,
                        'max' => 200)),
                    new Zend_Validate_Regex(APP_ALIAS_NAME_REGEX)
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StringToLower()
                )),
            'short-alias' => array(
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 1, 'max' => 55)),
                    new Zend_Validate_Regex(APP_ALIAS_NAME_REGEX)
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StripTags()
                )),
            'name' => array( // same as 'Title' for post
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 2, 'max' => 255))
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StripTags()
                )),
            'short-name' => array( // same as 'Title' for post
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 2, 'max' => 55))
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StripTags()
                )),
            'description' => array(
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 6))
                ),
                'filters' => array(
                    new Zend_Filter_StripTags(array(
                        'allowTags' => $this->_allowed_tags,
                        'allowAttribs' => $this->_allowed_attribs
                    )),
                    new Zend_Filter_StringTrim()
                )),
            'bln' => array(
                'validators' => array(
                    new Zend_Validate_Int(),
                    new Zend_Validate_StringLength(
                            array('min' => 0, 'max' => 1))
                )),
            'email' => array(
                'validators' => array(new Zend_Validate_EmailAddress(
                    Zend_Validate_Hostname::ALLOW_DNS, true)),
                'filters' => array(new Zend_Filter_StringToLower())
            ),
            // Db Auto Increment Identity Column
            'id' => array(
                'validators' => array(
                    new Zend_Validate_Int(),
                    new Zend_Validate_StringLength(
                            array('min' => 0, 'max' => 20))
                )),
            'html_id' => array(
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 2, 'max' => 255)),
                    new Zend_Validate_Regex('/[a-z\-\_\.\:]+/i')
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StripTags()
                )),
            'html_class' => array(
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 2, 'max' => 255)),
                    new Zend_Validate_Regex('/[\sa-z\-\_\.\:]+/i')
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StripTags()
                )),
            'uri' => array(
                'validators' => array(
                    new Zend_Validate_StringLength(
                            array('min' => 2, 'max' => 255)),
                    new Edm_Validate_Uri()
                ),
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StripTags()
                )),
            'date' => array(
                'validators' => array(
                    new Zend_Validate_Regex('/^\d{1,2}\/\d{1,2}\/\d{4}$/')
                )
            )
        );
        
        /**
         * Call the parent constructor.
         */
        parent::__construct($options);

        /**
         * Add any form decorators here and do any further form customization
         * here.
         */
        $this->removeDecorator('HtmlTag');
        $this->setElementDecorators(
                array(new Edm_Form_Decorator_Composite()));
        $this->setMethod('post');

        /**
         * Token
         */
        //$this->addElement('hash', 'token',
        //array('salt' => DEFAULT_TOKEN_SEED));

        /**
         *  Submit form button
         */
        $this->addElement('submit', 'submit', array(
            'label' => '',
            'value' => 'Submit'
        ));

        /**
         *  Clear  form button
         */
        $this->addElement('reset', 'reset', array(
            'label' => '',
            'value' => 'Reset'
        ));
    }

    /**
     * Gets validators for a particular key in user validator collection.
     * @param string $key
     * @return array of zend_validators
     */
    public function getValidators($key) {
        if (array_key_exists($key, $this->_validators_and_filters)
                && array_key_exists('validators', 
                        $this->_validators_and_filters[$key])) {
                return $this->_validators_and_filters[$key]['validators'];
        }
        else {
            throw new Exception('Form Validators for key &quot;'. $key .
                    '&quot; do not exists in user validator collection.');
        }
    }

    /**
     * Gets filters for a particular key in user filter collection.
     * @param string $key
     * @return array of zend_filters
     */
    public function getFilters($key) {
        if (array_key_exists($key, $this->_validators_and_filters)
                && array_key_exists('filters',
                        $this->_validators_and_filters[$key])) {
                return $this->_validators_and_filters[$key]['filters'];
        }
        else {
            throw new Exception('Form Filters for key &quot;'. $key .
                    '&quot; do not exists in user filter collection.');
        }
    }
}