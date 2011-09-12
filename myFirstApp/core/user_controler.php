<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * user controler for the App
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.user.controler
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */
class user_controler extends controler {
    /**
	 * Default action. Need to be implement in each package
	 */
	protected function home(){}

	/**
	 * Define global rights : you do what you whant. For each controler, you can define your own rights
     * see exemple in controler package
	 */
	protected function _rights(){}

    /**
     * You build your workflow to check if for this action, the user call id allowed to have the access
     * @param string : action name of the methode in the controler package
     * @example : in this methode, i implement an exemple. It's my choise but you can built your checking workflow :)
     */
    protected function checkRight($action) {
        $rights = $this->_rights();
		//pas de droits de définit : on refuse l'accès
		if(!is_array($rights) || count($rights) < 1) {
			throw new Exception(__('Droits non définis pour %package%:%action%',array('package' => $this->package_name, 'action' => $action)),403);
		}
		//test si * (everybody) peut accèder à l'action demandée
		if(isset($rights['*']) && ($rights['*'] == '*' || $rights['*'] == $action) ) {
			return true;
		}
		//user peut être logé
		if(isset($_SESSION['user'])) {
            if(isset($rights[$_SESSION['user']['role']]) && ($rights[$_SESSION['user']['role']] == '*' || isset($rights[$_SESSION['user']['role']][$action])) ) {
                if($this->checkSecureProtocol() === true){
                    //access alowed
                    return true;
                }
                //relog sur la home en https
                $url = HTTPS . 'dashboard.php';
                header("Location: $url");
                exit();
			}
		}
		throw new Exception(__('403 not auth'),403);
    }
}
?>