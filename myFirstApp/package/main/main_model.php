<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * DESCRIPTION
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package main.model
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class main_model extends user_model  {
    /**
     * This is a workflow. Usaly, do not make public function for the SQL request.
     * Make a workflow like this. This is usefull and allow you to factorize all your SQL request
     */
    public function test() {
        $this->_test();
    }

    /**
     * Simple request
     */
    private function _test(){
        $this->bdd();//init the SGBD connection

        $sql = 'SELECT
                      ' . self::TABLE_SUPPORT_TICKETS  . '.id
                FROM
                      ' . self::TABLE_SUPPORT_TICKETS;
        $values = array();
        
               	
        $this->_bdd->query($sql,$values);
        var_dump($this->_bdd->fetch_all_assoc());
    }
}
?>