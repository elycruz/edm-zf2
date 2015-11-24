<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/15/2015
 * Time: 8:30 PM
 */

namespace Edm\Db\ResultSet\Proto;


class TermTaxonomyProxyProto extends AbstractProto {
    /**
     * Valid Keys for Model
     * @var array
     */
    protected $_allowedKeysForProto = array(
        'term_taxonomy_id',
        'assocItemCount',
        'childCount'
    );

    protected $_formKey = 'termTaxonomyProxy';

}
