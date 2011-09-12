<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Manage URL and navigation.
 *
 */

class http {
	
	/** Durée d'une heure en secondes */
	const COOKIE_EXPIRE_HOUR  =     3600;
	/** Durée d'une journée en secondes	 */
	const COOKIE_EXPIRE_DAY   =    86400;
	/** Durée d'un mois de 30 jours en secondes */
	const COOKIE_EXPIRE_MONTH =  2592000;
	/** Durée d'une année de 365 jours en secondes */
	const COOKIE_EXPIRE_YEAR  = 31536000;

	/**
	 * Add a key / value to the cookie
	 *
	 * @param string $name Nom de la varaible à ajouter en cookie
	 * @param mixed $value Valeur de la varaible à ajouter en cookie
	 * @param int $expire Durée de validité de la variable
	 */
	public static function setCookieParameter($name, $value, $expire = self::COOKIE_EXPIRE_YEAR) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				self::setCookieParameter($name . '][' . $k, $v, $expire);
			}
		} else {
			setcookie('baobapp[' . $name . ']', $value, time() + $expire);
		}
	}

	/**
	 * Get the value of a key found in the cookie. If not return the default value
	 *
	 * @param string $name Nom de la variable à récupérer.
	 * @param mixed $default_value Valeurt à retourner si la variable n'est pas définie dans les cookies.
	 * @return mixed Cette fonction retourne la valeur de la variable demandée si elle est définie, sinon elle retourne la valeur de default_value.
	 */
	public static function getCookieParameter($name, $default_value = null) {
		return array_get_value(isset($_COOKIE['baobapp']) ? $_COOKIE['baobapp'] : null, $name, $default_value);
	}

	/**
	 * Change or create a key value in the $_GET / $_POST / $_REQUEST
	 *
	 * @param string $name Nom  du pramètre à changer ou créer.
	 * @param mixed $value Valeur à attribuer au paramètre.
	 */
	public static function setUrlParameter($name, $value) {
		$found = false;
		if (isset($_GET[$name])) {
			$_GET[$name] = $value;
			$found = true;
		}
		if (isset($_POST[$name])) {
			$_POST[$name] = $value;
			$found = true;
		}
		if (isset($_REQUEST[$name])) {
			$_REQUEST[$name] = $value;
			$found = true;
		}

		if (!$found) {
			$_GET[$name] = $value;
		}
	}

	public static function removeUrlParameter($name) {
		self::removeGetParameter($name);
		self::removePostParameter($name);
		self::removeRequestParameter($name);
	}

	public static function removePostParameter($name) {
		unset($_POST[$name]);
	}

	public static function removeGetParameter($name) {
		unset($_GET[$name]);
	}

	public static function removeRequestParameter($name) {
		unset($_REQUEST[$name]);
	}

	/**
	 * Get a parameter from an http call; Looking recursively in $_REQUEST, $_POST et $_GET.
	 *
	 * @param string $name Le nom du paramètre à récupérer.
	 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
	 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
	 */
	public static function getUrlParameter($name, $default_value = null) {
        if (
			($result = self::getGetParameter($name)) !== null ||
			($result = self::getPostParameter($name)) !== null ||
			($result = self::getRequestParameter($name)) !== null) {
				$default_value = $result;
		}

		return $default_value;
	}

	/**
	 * Get a parameter from an http call with the POST methode
	 *
	 * @param string $name Le nom du paramètre à récupérer.
	 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
	 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
	 */
	public static function getPostParameter($name, $default_value = null){
		return array_get_value($_POST, $name, $default_value);
	}

	/**
	 * Get a parameter from an http call with the REQUEST methode
	 *
	 * @param string $name Le nom du paramètre à récupérer.
	 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
	 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
	 */
	public static function getRequestParameter($name, $default_value = null){
		return array_get_value($_REQUEST, $name, $default_value);
	}

	/**
	 * Get a parameter from an http call with the GET methode
	 *
	 * @param string $name Le nom du paramètre à récupérer.
	 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
	 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
	 */
	public static function getGetParameter($name, $default_value = null){
		return array_get_value($_GET, $name, $default_value);
	}

	/**
	 * Get a parameter from an http call with the FILES methode
	 *
	 * @param string $name Le nom du paramètre à récupérer.
	 * @param mixed $default_value La valeur à retourner si le paramètre n'est pas définit.
	 * @return mixed Cette fonction retourne la valeur du paramètre http demandé si il est définit, sinon elle retourne la valeur par défaut.
	 */
	public static function getFileParameter($name){
		$result = array_get_value($_FILES, $name, null);
		if(!$result['tmp_name']) {
			return null;
		}
		return $result;
	}

	/**
	 * Http code.
	 *
	 * @param int $code Code à envoyer.
	 */
	public static function sendHttpStatusCode($code) {
		$codes = array(
			// OK
	        100 => array('HTTP/1.1', 'Continue'),
	        101 => array('HTTP/1.1', 'Switching Protocols'),
	        200 => array('HTTP/1.0', 'OK'),
	        201 => array('HTTP/1.0', 'Created'),
	        202 => array('HTTP/1.0', 'Accepted'),
	        203 => array('HTTP/1.0', 'Non-Authoritative Information'),
	        204 => array('HTTP/1.0', 'No Content'),
	        205 => array('HTTP/1.0', 'Reset Content'),
	        206 => array('HTTP/1.0', 'Partial Content'),
	        // Redirections
	        300 => array('HTTP/1.0', 'Multiple Choices'),
	        301 => array('HTTP/1.0', 'Permanently at another address - consider updating link'),
	        302 => array('HTTP/1.1', 'Found at new location - consider updating link'),
	        303 => array('HTTP/1.1', 'See Other'),
	        304 => array('HTTP/1.0', 'Not Modified'),
	        305 => array('HTTP/1.0', 'Use Proxy'),
	        306 => array('HTTP/1.0', 'Switch Proxy'), // No longer used, but reserved
	        307 => array('HTTP/1.0', 'Temporary Redirect'),
	        // Erreur de communication
	        400 => array('HTTP/1.0', 'Bad Request'),
	        401 => array('HTTP/1.0', 'Authorization Required'),
	        402 => array('HTTP/1.0', 'Payment Required'),
	        403 => array('HTTP/1.0', 'Forbidden'),
	        404 => array('HTTP/1.0', 'Not Found'),
	        405 => array('HTTP/1.0', 'Method Not Allowed'),
	        406 => array('HTTP/1.0', 'Not Acceptable'),
	        407 => array('HTTP/1.0', 'Proxy Authentication Required'),
	        408 => array('HTTP/1.0', 'Request Timeout'),
	        409 => array('HTTP/1.0', 'Conflict'),
	        410 => array('HTTP/1.0', 'Gone'),
	        411 => array('HTTP/1.0', 'Length Required'),
	        412 => array('HTTP/1.0', 'Precondition Failed'),
	        413 => array('HTTP/1.0', 'Request Entity Too Large'),
	        414 => array('HTTP/1.0', 'Request-URI Too Long'),
	        415 => array('HTTP/1.0', 'Unsupported Media Type'),
	        416 => array('HTTP/1.0', 'Requested Range Not Satisfiable'),
	        417 => array('HTTP/1.0', 'Expectation Failed'),
	        449 => array('HTTP/1.0', 'Retry With'), // Microsoft extension
	        // Erreur serveur
	        500 => array('HTTP/1.0', 'Internal Server Error'),
	        501 => array('HTTP/1.0', 'Not Implemented'),
	        502 => array('HTTP/1.0', 'Bad Gateway'),
	        503 => array('HTTP/1.0', 'Service Unavailable'),
	        504 => array('HTTP/1.0', 'Gateway Timeout'),
	        505 => array('HTTP/1.0', 'HTTP Version Not Supported'),
	        509 => array('HTTP/1.0', 'Bandwidth Limit Exceeded') // not an official HTTP status code
	    );

	    header($codes[$code][0] . ' ' . $code . ' ' . $codes[$code][1]);
	    if($code >=400 && $code <500 ) {
	    	exit();
	    }
	}


	/**
	 * Redirige automatiquement l'utilisateur vers l'url définie
	 *
	 * @param string $url Url vers laquel rédiriger.
	 */
	public static function redirect($url, $code = 301){
		
	    if (!headers_sent()) {    //If headers not sent yet... then do php redirect
	    	self::sendHttpStatusCode($code);
	        header('Location: '.$url);
	        exit;
	    } else {                    //If headers are sent... do java redirect... if java disabled, do html redirect.
	        echo
			'<script type="text/javascript">window.location.href="'.$url.'";</script>' .
			'<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
		    exit();
		}
	}
}
?>