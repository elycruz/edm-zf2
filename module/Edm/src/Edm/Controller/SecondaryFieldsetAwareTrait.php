<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Controller;

use Edm\Model\SecondaryFieldsetDataModel;

/**
 * Description of SecondaryFieldsetAwareTrait
 *
 * @author ElyDeLaCruz
 */
trait SecondaryFieldsetAwareTrait {

    /**
     * Secondary Fieldset Data Model.
     * @var Edm\Model\SecondaryFieldsetDataModel
     */
    protected $secondaryFieldsetDataModel;
    
    /**
     * Lazy loads our secondary fieldset data model.
     * @param String $fieldsetAlias
     * @return Edm\Model\SecondaryFieldsetDataModel
     */
    public function getSecondaryFieldsetDataModel($fieldsetAlias = null) {
        if (empty($this->secondaryFieldsetDataModel)) {
            $this->secondaryFieldsetDataModel = 
                    new SecondaryFieldsetDataModel($fieldsetAlias);
        }
        return $this->secondaryFieldsetDataModel;
    }

}
