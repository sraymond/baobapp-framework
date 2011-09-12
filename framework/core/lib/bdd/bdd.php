<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.lib.bdd
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class bdd {
    
    //resource de connexion MySQL
    protected $_dbh;
    //ressource de resulta sur une requette
    private  $_dbh_statement;
	//dsn
	private $_dsn;
	//login
	private $_login;
	//passwd
	private $_passwd;
	//option
    private $_option;
    
    /**
     * constructeur
     *
     * @param string $dsn
     * @param string $login
     * @param string $passwd
     * @param array $option
     */
    function __construct($dsn,$login,$passwd,$option = array()) {
        $this->_dsn = $dsn;
    	$this->_login = $login;
    	$this->_passwd = $passwd;
    	$this->_option = $option;
    	$this->_dbh = null;
    } 

    
    /**
     * préaration d'une requette et exécution de cette dernière
     *
     * @param string    requette $sql
     * @param array     Valeur qui seront injectées dans la requette sql
     * @return true
     * @exception $this->error()
     */
    public function query($sql,$value = array(),$force_use_same_dbh = false) {
    	//initialisation de la ressource dbh 
    	$this->connexion();
    	try {
	        $this->_dbh_statement = $this->_dbh->prepare($sql);
	        if(count($value) > 0) {
	            $result = $this->_dbh_statement->execute($value);
	        }else {
	            $result = $this->_dbh_statement->execute();
	        }
	        return $result;
    	} catch (PDOException $e) {
            throw new Exception($e->getMessage() . $e->getTraceAsString(),500);
    	}
    }
    
    public function fetch_assoc() {
        $result = $this->_dbh_statement->fetch(PDO::FETCH_ASSOC);
        $this->_dbh_statement->closeCursor();
        return $result;
    }
    
    public function fetch_all_assoc() {
        $result =  $this->_dbh_statement->fetchAll(PDO::FETCH_ASSOC);
        $this->_dbh_statement->closeCursor();
        return $result;
    }
    
    public function converteTableKeyValue($base,$details) {
    	foreach ($details as $clef => $value) {
    		$base[$value['key']] = $value['value'];
    	}
    	return $base;	
    }
    
    public function lastInsertId() {
        return $this->_dbh->lastInsertId();
        $this->_dbh_statement->closeCursor();    
    }
    
    public function closedStatement() {
        $this->_dbh_statement->closeCursor();    
    }
    
    public function error($error_message) {
    	throw new Exception($error_message);
    }
    
    /**
     * Enter description here...
     *
     */
    public function beginTransaction() {
    	$this->connexion();
    	$this->_dbh->beginTransaction();	
    }
    
    /**
     * Si tout c'est bien pass� dans la transaction : la requette est jou�e
     *
     */
    public function commit() {
    	$this->_dbh->commit();	
    }
    
    /**
     * Il y a eu un pb lors du traitement de la ou des requettes ou dans la proc�dure de v�rification
     * on n'ex�cute pas la requette
     *
     */
    public function rollBack() {
    	$this->_dbh->rollBack();	
    }
    
    /**
     * retourne la ressource d'acc�s � la base de donn�e
     *
     * @return object $_dbh
     */
    private function connexion() {
    	if($this->_dbh === null) {
    		if(count($this->_option) > 0) {
            	$this->_dbh = new PDO($this->_dsn,$this->_login,$this->_passwd,$this->_option);   
        	}else {
            	$this->_dbh = new PDO($this->_dsn,$this->_login,$this->_passwd);
        	}
            $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	}
    }
}
?>