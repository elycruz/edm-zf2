<?php

/*
 * Edm CMS - The Extensible Data/Content Management System 
 * 
 * LICENSE 
 * 
 * Copyright (C) 2011-2012  Ely De La Cruz http://www.elycruz.com
 * 
 * All rights under the GNU General Public License v3.0 or later 
 * (see http://opensource.org/licenses/GPL-3.0) and the MIT License
 * (see http://opensource.org/licenses/MIT) reserved.
 * 
 * All questions and/or comments concerning the software and its licenses 
 * can be directed to: info -at- edm -dot- elycruz -dot- com
 * 
 * If you did not received a copy of these licenses with this software
 * request a copy at: license -at- edm -dot- elycruz -dot- com
 */
namespace Edm\Db;
/**
 * @author ElyDeLaCruz
 */
interface DbDataHelperAware {
    public function getDbDataHelper();
    public function setDbDataHelper(DbDataHelper $dbDataHelper);
}
