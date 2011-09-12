<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.user.model
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

/**
 * singleton bdd
 * Build and choos your way how to connect to your BDD
 *
 * @return object bdd
 *
 */
function getbdd () {

    include(ROOT_APP_PATH .'/config/config_bdd.php');
    global $phpunit;
    static $bdd_obj = null;

    switch($phpunit) {
    	case true:
    		if($bdd_obj === null) {
		        $bdd_obj = eval('return new phpunitDB(\'' . $bdd_login . '\',\'' . $bdd_passwd . '\',\'' . $bdd_user_db_name .'\',\''. $bdd_host .'\'  );');
		    }
    	break;

    	default:
		    require_once(ROOT_FRAMEWORK_PATH . '/core/lib/bdd/' . $driver . '.php');
		    if($bdd_obj === null) {
		        $bdd_obj = eval('return new ' . $driver . '(\'' . $bdd_login . '\',\'' . $bdd_passwd . '\',\'' . $bdd_user_db_name .'\',\''. $bdd_host .'\'  );');
		    }
	    break;
    }
    return $bdd_obj;
}

class user_model extends model {
    
    /**
     * Define the name of your table : Use the CONSTANT name in the SQL request.
     * This allow you to modify as well the name of your DB without a break
     */
    const TABLE_SUPPORT_TICKETS	        = 'support_tickets';
    const TABLE_SUPPORT_TICKETS_DETAILS	= 'support_ticket_details';
    const TABLE_SUPPORT_EVENTS	        = 'support_events';
    
    //gestion des customers
    const TABLE_CUSTOMERS               = 'customers';


}
?>