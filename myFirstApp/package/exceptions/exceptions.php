<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * DESCRIPTION
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package exception.controler
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class exceptions extends user_controler {
	
    public function __construct() {
		$this->package_name = 'exceptions';
        parent::__construct();
	}
	
    /**
	 * Définition des droits
	 * @return array Cette fonction retourne un tableau de droits action => droits
	 */
	protected function _rights() {
        return array(
			'*'					=> '*'
		);
	}

	/**
	 * Méthode par défaut obligatoire
	 */
	protected function home() {
	    $this->getView()->home();
	}

    protected function error500() {
        $this->getView()->error500();
    }
}
?>