<?php

namespace Edm\Db\TableGateway;

class SecurityQuestionUserRelTable extends BaseTableGateway {

    protected $alias = 'securityQuestionUserRel';
    protected $table = 'security_question_user_relationships';
    protected $modelClass = \Edm\Model\SecurityQuestionUserRel;

}