<?php

namespace Edm\Db\ResultSet\Proto;

class ContactUserRelProto extends AbstractProto {

    /**
     * Allowed keys for `toArray` and `setAllowedKeysForProto` amongst other methods.
     * @var array
     */
    protected $_allowedKeysForProto = [
        'screenName',
        'email'
    ];

    /**
     * @var string
     */
    protected $_formKey = 'contactUserRel';

}
