<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * DESCRIPTION
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package main.controler
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class main extends user_controler {
	
    public function __construct() {
		$this->package_name = 'main';
        parent::__construct();
	}
	
    /**
	 * Définition des droits
	 * @return array Cette fonction retourne un tableau de droits action => droits
	 */
	protected function _rights() {
        return array(
			'*'=> '*'
		);
	}

	/**
	 * Méthode par défaut obligatoire
	 */
	protected function home() {
        //$test = $this->getModel()->test();
        $this->getView()->home();
	}
}
?>