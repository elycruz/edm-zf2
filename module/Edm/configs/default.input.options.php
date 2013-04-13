<?php

/**
 * Common input filter definitions used in our forms
 * @var array
 */
return array(
    
    /**
     * Id
     * @var bigint(20)
     */
    'id' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/^\d{1,20}$/'
                )
            )
        )
    ),
    
    /**
     * Alias
     * @var varchar(200)
     */
    'alias' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/^[a-z\d\-_]{2,200}$/i'
                )
            )
        ),
        'filters' => array(
            array('name' => 'StringToLower'),
        )
    ),
    
    /**
     * Short Alias
     * @var varchar(55)
     */
    'short-alias' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/^[a-z\d\-_]{2,55}$/i'
                )
            )
        ),
        'filters' => array(
            array('name' => 'StringToLower'),
        )
    ),
    
    /**
     *  Name
     * @var varchar(55)
     */
    'name' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/^[\w]{2,255}$/i'
                )
            )
        ),
        'filters' => array(
            array('name' => 'StripTags'),
            array('name' => 'StringTrim')
        )
    ),
    
    /**
     * Short Name
     * @var varchar(55)
     */
    'short-name' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/^[\w]{2,55}$/i'
                )
            )
        ),
        'filters' => array(
            array('name' => 'StripTags'),
            array('name' => 'StringTrim')
        )
    ),
    
    /**
     * Boolean
     * @var 
     */
    'boolean' => array(
        'filters' => array(
            array('name' => 'Boolean',
                'options' => array(
                    'type' => 'all'
                )
            )
        )
    ),
    
    /**
     * Email
     * @var varchar(255)
     */
    'email' => array(
        'validators' => array(
            array('name' => 'Email')
        ),
        'filters' => array(
            array('name' => 'StringToLower')
        )
    ),
    
    /**
     * Password
     * @var varchar(64)
     */
    'password' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/^[a-z\d\-_]{6,32}$/'
                ))
        )
    ),
    
    /**
     * Html Id
     * @var varchar(255) 
     */
    'html_id' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/[a-z\d\-\_\.\:]{2,200}/i'
                )
            )
        )
    ),
    
    /**
     * Html Class
     * @var varchar(255) 
     */
    'html_class' => array(
        'validators' => array(
            array('name' => 'Regex',
                'options' => array(
                    'pattern' => '/[a-z\d\s\t\n\r\-\_\.\:]{2,200}/i'
                )
            )
        ),
        'filters' => array(
            array('name' => 'StringTrim')
        )
    ),
    
    /**
     * Description
     * @var longtext
     */
    'description' => array(
        'filters' => array(
            array('name' => 'StripTags',
                'options' => array(
                    'allowTags' => array(
                        'div', 'span', 'object', 'h1', 'h2', 'h3', 'h4',
                        'h5', 'h6', 'hr', 'p', 'blockquote', 'pre', 'a', 'abbr', 'acronym',
                        'address', 'big', 'cite', 'code', 'del', 'dfn', 'em',
                        'img', 'ins', 'q', 's', 'samp', 'small', 'strong', 'sub',
                        'sup', 'tt', 'var', 'b', 'i', 'dl', 'dt', 'dd', 'ol', 'ul', 'li',
                        'fieldset', 'label', 'legend', 'table', 'caption', 'tbody',
                        'tfoot', 'thead', 'tr', 'th', 'td'
                    ),
                )
            ),
            array('name' => 'StringTrim')
        )
    )
    
);
