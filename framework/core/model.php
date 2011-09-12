<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.model
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

abstract class model {
    
    protected $_bdd = null;
    
    function __construct() {
        $this->bdd();
    }
    
    /**
     * Retourne un objet bdd instancié
     *
     * @return object bdd
     */
    protected function bdd() {
        if($this->_bdd === null) {
            try {
                $this->_bdd = getbdd();
            } catch (Exception $bdd) {
                throw new Exception('Problème sur la bdd ->' . $bdd->getTraceAsString(),500);
            }    
        }
    }
    
    /**
     * Retourne un model instancié
     * @param nom du model $model_name
     * @return object
     */
    protected function _getModel ($model_name) {
        return new $model_name();
    }
}
?>