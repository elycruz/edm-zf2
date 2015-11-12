<?php

namespace Edm\Db\TableGateway;

class DateInfoTable extends BaseTableGateway {

    protected $alias = 'dateInfo';
    protected $table = 'date_info';
    protected $modelClass = \Edm\Model\DateInfo;

}