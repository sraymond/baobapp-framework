<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0 : drivers PDO pour mysql
 * @package core.lib.bdd
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class mysql extends bdd {

    function __construct($login,$passwd,$dbase,$host = 'localhost') {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbase;
        parent::__construct($dsn,$login,$passwd,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
    }
}
?>