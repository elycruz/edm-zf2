<?php

/**
 * Common input filter configs/definitions used for our forms.
 * @var array <string => array <validators[[]], filters[[]]> >
 */
return [

    /**
     * Id - bigint(20)
     * @var array <validators<[]>>
     */
    'id' => [
        'validators' => [
            ['name' => 'Digits'],
            ['name' => 'StringLength',
                'options' => [
                    'min' => 0,
                    'max' => 20
                ]
            ]
        ]
    ],

    'int' => [
        'validators' => [
            ['name' => 'Digits']
        ]
    ],

    /**
     * Alias - varchar(200)
     * @var array
     */
    'alias' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/^[\w\-]{2,200}$/i'
                ]
            ]
        ],
        'filters' => [
            ['name' => 'StringToLower'],
        ]
    ],

    /**
     * Short Alias - varchar(55)
     * @var array
     */
    'short-alias' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/^[\w\-]{2,55}$/i'
                ]
            ]
        ],
        'filters' => [
            ['name' => 'StringToLower'],
        ]
    ],

    /**
     *  Name - varchar(55)
     * @var array
     */
    'name' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/^[\w\W\s\d\`]{2,255}$/i'
                ]
            ]
        ],
        'filters' => [
            ['name' => 'StripTags'],
            ['name' => 'StringTrim']
        ]
    ],

    /**
     * Short Name - varchar(55)
     * @var array
     */
    'short-name' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/^[a-z\w\s\d\`]{2,55}$/i'
                ]
            ]
        ],
        'filters' => [
            ['name' => 'StripTags'],
            ['name' => 'StringTrim']
        ]
    ],

    /**
     * Boolean
     * @var array
     */
    'boolean' => [
        'filters' => [
            ['name' => 'Boolean',
                'options' => [
                    'type' => 'all'
                ]
            ]
        ]
    ],

    /**
     * Email - varchar(255)
     * @var array
     */
    'email' => [
        'validators' => [
            ['name' => 'EmailAddress']
        ],
        'filters' => [
            ['name' => 'StringToLower']
        ]
    ],

    /**
     * Password - varchar(64)
     * @todo create friendlier password mismatch message
     * @var array
     */
    'password' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/^[a-z\d\-_]{6,32}$/',
                    'message' => 'Password doesn\'t match password pattern.'
                ]]
        ]
    ],

    /**
     * Html Id - varchar(200)
     * @var array
     */
    'html_id' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/[a-z\d\-\_\.\:]{2,200}/i'
                ]
            ]
        ]
    ],

    /**
     * Html Class - varchar(200)
     * @var array
     */
    'html_class' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/[a-z\d\s\t\n\r\-\_\.\:]{2,200}/i'
                ]
            ]
        ],
        'filters' => [
            ['name' => 'StringTrim']
        ]
    ],

    /**
     * Description - longtext
     * @var array
     */
    'description' => [
        'filters' => [
            ['name' => 'StripTags',
                'options' => [
                    'allowTags' => [
                        'div', 'span', 'object', 'h1', 'h2', 'h3', 'h4',
                        'h5', 'h6', 'hr', 'p', 'blockquote', 'pre', 'a', 'abbr', 'acronym',
                        'address', 'big', 'cite', 'code', 'del', 'dfn', 'em',
                        'img', 'ins', 'q', 's', 'samp', 'small', 'strong', 'sub',
                        'sup', 'tt', 'var', 'b', 'i', 'dl', 'dt', 'dd', 'ol', 'ul', 'li',
                        'fieldset', 'label', 'legend', 'table', 'caption', 'tbody',
                        'tfoot', 'thead', 'tr', 'th', 'td'
                    ],
                ]
            ],
            ['name' => 'StringTrim']
        ]
    ],

    /**
     * Screen Name - varchar(32) - [a-z\d]{6,32}
     * @var array
     */
    'screen-name' => [
        'validators' => [
            ['name' => 'Alnum'],
            ['name' => 'StringLength',
                'options' => [
                    'min' => 6, 'max' => 32]]
        ]],

    /**
     * Activation Key - varchar(32) - [a-z\d]{32}
     * @var array
     */
    'activation-key' => [
        'validators' => [
            ['name' => 'Regex',
                'options' => [
                    'pattern' => '/^[a-z\d]{32}$/i',
                    'message' => 'Activation key is invalid.'
                ]]
        ]
    ]

];
