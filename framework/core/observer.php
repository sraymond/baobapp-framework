<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.observer
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class observer {
    
    private $_ressource;
    
    function __construct($unit_test = false) {
        $this->setRessource($unit_test);
    }
    public function getRessourceName() {
        return $this->_ressource['_p'];
    }
    
    public function getParam() {
        return $this->_ressource['param'];
    }
    
    public function getRessource() {
        return $this->_ressource;
    }
    
    /**
     * If the ressource exist then she would be instanciate later by the controler
     * If not : just throw a new 404 exception allow you to display a 404 (have a look in the index.php bootstrap)
     */
    public function checkExisteRessource() {
        if(file_exists(RESSOURCE_PACKAGE_PATH . '/' . $this->_ressource['_p'] . '/' . $this->_ressource['_p'] . '.php') !== true) {
            $message =  '   User message : La ressource demandée n\'existe pas <br />
                            Request_URI : ' . $_SERVER['REQUEST_URI'] . '<br />
                            Package demandé : ' . $this->_ressource['_p'] . '<br />
                            Path : ' . RESSOURCE_PACKAGE_PATH . '<br />
                            Fichier : ' . __FILE__ . '<br />
                            Function : ' . __FUNCTION__ . '<br />
                            Line : ' . __LINE__;
            throw new Exception($message,404);
        }
    }

    /**
     * Analyse the request and put into an array informations. You can overwrite in the user_observer class
     * Action => _a
     * Package => _p
     * Parameters
     * @param <type> $mode
     * @return <type>
     */
    protected function setRessource($unit_test) {
        
        if($unit_test === true) {
            return;
        }
        $ressource = parse_url($_SERVER['REQUEST_URI']);
        $list_item = array();
        $new_list_item = array();
        
        ## cas de "/", "/?_a=xx&_p=zzz=ccc , "/index.php" et "/index.php?_a=dede&_p=xx&szszs=ss..."
        if($ressource['path'] == '/' || preg_match("/\index\b/i",$ressource['path'])) {
            if(isset($ressource['query'])){
                parse_str($ressource['query'],$list_item);
            }
            $new_list_item = $this->findAndInitParameter($list_item);
        }else{
            $trim_call = (explode('/', $ressource['path']));

            switch(count($trim_call)) {
                ##cas du type /login.php?dededed ou /login?dededed ou /login ou /login.php
                case 2:
                    $this->_ressource['_p'] = (preg_match("/.php/i",$trim_call[1]))? substr($trim_call[1], 0, -4): $trim_call[1];
                    if(isset($ressource['query'])) {
                        parse_str($ressource['query'],$list_item);
                    }
                    $new_list_item = $this->findAndInitParameter($list_item);
                    break;
                ##cas du type /xxx/zzz/?name1=val1&name2=val2...
                case 4:
                    $this->_ressource['_p'] = $trim_call[1];
                    $this->_ressource['param']['_a'] = $trim_call[2];
                    if(isset($ressource['query'])) {
                        parse_str($ressource['query'],$list_item);
                        $new_list_item = $this->findAndInitParameter($list_item);
                    }
                    break;
                default:
                    $message = ' *** Observer : setRessource()
                                 *** Case : default
                                 *** Request URI : ' .$_SERVER['REQUEST_URI'];
                    throw new Exception($message);
            }
        }
        
        ## Traitement de $ressource['query'] à injecter dans $_GET tout en sup les _p & _a
        if(isset($new_list_item['_p'])) {
            unset($new_list_item['_p']);
        }
        if(isset($new_list_item['_a'])) {
            unset($new_list_item['_a']);
        }
        if(count($new_list_item) > 0) {
            foreach($new_list_item as $key=>$val) {
                $_GET[$key] = $val;
            }
        }
    }

    /**
     * Prend en entrée un tableau clé valeur issu de request_ury pour la recherche de paramettre
     * @param array $list_item
     * @return array retourne le même tableau débarassé de _a & _p si jamais trouvé
     */
    private function findAndInitParameter($list_item) {
        #recherche des paramettres _a & _p dans $_POST
        if(!isset($this->_ressource['param']['_a']) && isset($_POST['_a'])) {
            $this->_ressource['param']['_a'] = $_POST['_a'];
            unset($_POST['_a']);
        }
        if(!isset($this->_ressource['_p']) && isset($_POST['_p'])) {
            $this->_ressource['_p'] = $_POST['_p'];
            unset($_POST['_p']);
        }
        #recherche des paramettres _a & _p dans $_GET si non trouvé dans $_POST
        if(!isset($this->_ressource['param']['_a']) && isset($_GET['_a']) ) {
            $this->_ressource['param']['_a'] = $_GET['_a'];
            unset($_GET['_a']);
        }
        if(!isset($this->_ressource['_p']) && isset($_GET['_p']) ) {
            $this->_ressource['_p'] = $_GET['_p'];
            unset($_GET['_p']);
        }
        #recherche des paramettres _a & _p dans $ressource['query']
        if(isset($list_item['_p']) && !isset($this->_ressource['_p'])) {
            $this->_ressource['_p'] = $list_item['_p'];
            unset($list_item['_p']);
        }
        if(isset($list_item['_a']) && !isset($this->_ressource['param']['_a']) ) {
            $this->_ressource['param']['_a'] = $list_item['_a'];
            unset($list_item['_a']);
        }
        # traite le cas de la ressource _a & _p = '' || = main || non définit
        if(!isset($this->_ressource['param']['_a']) || $this->_ressource['param']['_a'] == '' || $this->_ressource['param']['_a'] == 'main' ) {
            $this->_ressource['param']['_a'] = 'home';
        }
        if(!isset($this->_ressource['_p']) || $this->_ressource['_p'] == '') {
            $this->_ressource['_p'] = 'main';
        }
        return $list_item;
    }
   
}

/**
 * permet de récupérer la valur d'une variable qui a été posté en GET POST FILE
 *
 */


/**
 * Add to a cookie a variable
 *
 * @param string $name Name of the variable / Nom de la varaible à ajouter en cookie
 * @param mixed $value Value associate to the name / Valeur de la varaible à ajouter en cookie
 * @param int $expire Time / Durée de validité de la variable
 */
function setCookieParameter($name, $value, $expire = COOKIE_EXPIRE_YEAR) {
	setcookie('baobapp[' . $name . ']', $value, time() + $expire);
}

/**
 * Return a value find in the cookie
 *
 * @param string $name Nom de la variable à récupérer.
 * @param mixed $default_value Valeurt à retourner si la variable n'est pas définie dans les cookies.
 * @return mixed Cette fonction retourne la valeur de la variable demandée si elle est définie, sinon elle retourne la valeur de default_value.
 */
function getCookieParameter($name, $default_value = null) {
	return getParameterFrom(isset($_COOKIE[getConfig('cookie','name','MyApp')]) ? $_COOKIE[getConfig('cookie','name','MyApp')] : null, $name, $default_value);
}

/**
 * Change the value of an entry point of $_POST / $_GET or create the entry point
 *
 * @param string $name Nom  du pramètre à changer ou créer.
 * @param mixed $value Valeur à attribuer au paramètre.
 */
function setUrlParameter($name, $value) {
	$found = false;
	if (isset($_REQUEST[$name])) {
		$_REQUEST[$name] = $value;
		$found = true;
	}
	if (isset($_POST[$name])) {
		$_POST[$name] = $value;
		$found = true;
	}
	if (isset($_GET[$name])) {
		$_GET[$name] = $value;
		$found = true;
	}

	if (!$found) {
		$_POST[$name] = $value;
	}
}

/**
 * Return the value of a parameter $_POST / $_GET / $_REQUEST
 *
 * @param string $name Le nom du paramètre à récupérer.
 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
 */
function getUrlParameter($name, $default_value = null) {
	if (($result = getPostParameter($name)) !== null) {
		return $result;
	} else if (($result = getGetParameter($name)) !== null) {
		return $result;
	} else if (($result = getRequestParameter($name)) !== null) {
		return $result;
	}

	if ($name == '_a') {
		if ($result = _lookForAction($_POST)) {
			return $result;
		} else if ($result = _lookForAction($_GET)) {
			return $result;
		} else if ($result = _lookForAction($_REQUEST)) {
			return $result;
		}
	}

	setUrlParameter($name, $default_value);
	return $default_value;
}

/**
 * Recherche un paramètre d'action définit sous la forme name.x (cas des formulaires soumis par image)
 *
 * @param array $array Tableau dans lequel chercher le paramètre
 * @return stringRetourne la valeur de l'action ou NULL.
 */
function _lookForAction(&$array) {
	// Cas particulier des actions dans les formulaires
	foreach ($array as $k => $v) {
		if (strpos($k, 'a_') === 0) {
			$k = substr($k, 2);
			if (strpos($k, '_x')) {
				$k = substr($k, 0, -2);
			}
			$array['action'] = $k;
			return $k;
		}
	}
	return null;
}

/**
 * Return a value send by POST action
 *
 * @param string $name Le nom du paramètre à récupérer.
 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
 */
function getPostParameter($name, $default_value = null){
	return getParameterFrom($_POST, $name, $default_value);
}

/**
 * return a value send by REQUEST action
 *
 * @param string $name Le nom du paramètre à récupérer.
 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
 */
function getRequestParameter($name, $default_value = null){
	return getParameterFrom($_REQUEST, $name, $default_value);
}

/**
 * return a value send by GET action.
 *
 * @param string $name Le nom du paramètre à récupérer.
 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
 */
function getGetParameter($name, $default_value = null){
	return getParameterFrom($_GET, $name, $default_value);
}

/**
 * return the temp name send by FILES action
 *
 * @param string $name Le nom du paramètre à récupérer.
 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
 */
function getFileParameter($name){
	$result = getParameterFrom($_FILES, $name, null);
	if(!$result['tmp_name']) {
		return null;
	}
	return $result;
}

/**
 * return a value save in the SESSION value
 *
 * @param string $name Le nom du paramètre à récupérer.
 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
 * @return mixed Cette fonction retourne la valeur du paramètre session demandé si il est définit, sinon elle retourne la valeur par défaut.
 */
function getSessionParameter($name, $default_value = null) {
	if(isset($_SESSION[$name])){
		return $_SESSION[$name];
	}
	return $default_value;
}

/**
 * Save a value in the SESSION
 *
 * @param string $name Nom de la variable
 * @param mixed $value Valeur de la variable
 */
function setSessionParameter($name, $value) {
	$_SESSION[$name] = $value;
}

/**
 * remove a value from $_GET or $_POST array
 *
 */
function unsetUrlParameter($name) {
    if(isset($_GET[$name])) {
        unset($_GET[$name]);    
    }
    if(isset($_POST[$name])) {
        unset($_POST[$name]);    
    }    
}
?>