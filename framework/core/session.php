<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Gestion des sessions.
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.session
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class session {
	
	public function __construct() {
        $this->startSession();
	}

    public function destroySession() {
        if(isset($_SESSION)) {
            session_destroy();
        }
    }

	protected function startSession() {
        session_start();
	}
}
?>