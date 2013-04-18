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

/**
 * Description of PostTermRel
 *
 * @author ElyDeLaCruz
 */
class Model_PostTermRel extends Edm_Db_AbstractTable 
{
    protected $_name = 'post_term_relationships';

    public function createPostTermRel(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updatePostTermRel($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'post_id'));
    }

    public function deletePostTermRel($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'post_id'));
        
    }
    
    public function setTermTaxonomyId($id, $termTaxId) {
        return $this->update(array('term_taxonomy_id' => $termTaxId),
                $this->getWhereClauseFor($id, 'post_id'));
    }

    public function setStatus($id, $status) {
        return $this->update(array('status' => $status),
                $this->getWhereClauseFor($id, 'post_id'));
    }
}