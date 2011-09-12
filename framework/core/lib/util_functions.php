<?php
	
	/**
	 * Replace a string like %key% with a correspondance in an array
     * Remplace les chaine de type %key% d'une chaine par sa correspondance dans un tableau.
	 *
	 * @param String $text The string tha you would like to change / La chaine de caractères à modifier
	 * @param array $params The array / Tableau de correspondance
	 * @return String return the string change / retourne le texte modifié.
	 */
	function nsprintfn($text, $params = array()) {
		if (!$params) {
			return $text;
		}
		$tmp = array();
		foreach($params as $k => $v) {
			$tmp['%' . $k . '%'] = $v;
		}
		return str_replace(array_keys($tmp), array_values($tmp), $text);
	}

	/**
	 * Return athe value of the entry name in an array
     * Récupère une entrée d'un tableau ou la valeur par défaut définie.
	 *
	 * @param array $source the array tha we are going to search / Le tableau dans lequel rechercher la valeur.
	 * @param string $name The name of the entry point tha we are looking for / Le nom du paramètre à récupérer.
	 * @param mixed  $default_valueIf the name is not define in the array, we return this default value / La valeur à retourner si le paramètre n'est pas définit.
	 * @return mixed return the search value name / retourne la valeur du paramètre demandé si il est définit dans le tableau, sinon elle retourne la valeur par défaut.
	 */
	function array_get_value($source, $name, $default_value = null){
		$array = strlen($name) > 2 && substr($name, -2) == '[]';
		if ($array) {
			$name = substr($name, 0, -2);
		}

		if($source && isset($source[$name]) && $source[$name] !== null) {
			$default_value = $source[$name];
		}

		if ($default_value && $array && !is_array($default_value)) {
			$default_value = array($default_value);
		}

		return $default_value;
	}

	/**
	 * Indicate if the string starts with the defined string.
	 *
	 * @param string $haystack
	 * @param mixed $needle
	 * @param boolean $case_sensitive Indicate if the comparison is case sensitive
	 *
	 * @return boolean This function returns TRUE if the haystack starts with the needle, else it returns FALSE
	 */
	function str_starts_with($haystack, $needle, $case_sensitive = false) {
		if (!$case_sensitive) {
			return stripos($haystack, $needle) === 0;
		} else {
			return strpos($haystack, $needle) === 0;
		}
	}

	/**
	 * Indicate if the string ends with the defined string.
	 *
	 * @param string $haystack
	 * @param mixed $needle
	 * @param boolean $case_sensitive Indicate if the comparison is case sensitive
	 *
	 * @return boolean This function returns TRUE if the haystacke ends with the needle, else it returns FALSE
	 */
	function str_ends_with($haystack, $needle, $case_sensitive = false) {
		if ($case_sensitive) {
			$haystack = strtolower($haystack);
			$needle = strtolower($needle);
		}

		$needle_length = strlen($needle);

		if ($needle_length > strlen($haystack)) {
			return false;
		}

		return $needle === substr($haystack, -$needle_length);

		//return strripos($haystack, $needle) === (strlen($haystack) - strlen($needle));
	}

	/**
	 * Explode a string in a tree using multiple delimiters.
	 *
	 * @example
	 * 	explodeTree(array(";", ','), 'a,b,c;d,e;') =>
	 * 		[0] => array
	 * 			[0] => a
	 * 			[1] => b
	 * 			[2] => c
	 * 		[1] => array
	 * 			[0] => d
	 * 			[1] => e
	 * 		[2] => ''
	 *
	 * 	explodeTree(array(",", ';'), 'a,b,c;d,e;') =>
	 * 		[0] => a
	 * 		[1] => b
	 * 		[2] => array
	 * 			[0] => c
	 * 			[1] => d
	 * 		[3] => array
	 *	 		[0] => e
	 *	 		[1] => ''
	 *
	 * @param array $delimiters Delimiters sorted from top to bottom of the tree
	 * @param string $string The string to explode
	 *
	 * @return array This function returns a tree of arrays
	 */
	function explodeTree($delimiters, $string) {
		$result = explode(array_shift($delimiters), $string);
		if(count($delimiters) > 0) {
			foreach ($result as $k => $v) {
				$result[$k] = explodeTree($delimiters, $v);
			}
		}
		return $result;
	}

	/**
	 * Extract a table of values for the defined index of each item
	 *
	 * @param array $array
	 * @param mixed $index
	 * @return array
	 */
	function array_extract($array, $index) {
		foreach ($array as $k => $v) {
			if (isset($v[$index])) {
				$array[$k] = $v[$index];
			} else {
				unset($array[$k]);
			}
		}

		return $array;
	}

	/**
	 * Implode an array using only the defined index.
	 *
	 * @param string $glue
	 * @param array $pieces
	 * @param mixed $index
	 * @return string
	 */
	function implode_index($glue, $pieces, $index) {
		return implode($glue, array_extract($pieces, $index));
	}

	function arrayToPhpArrayCode($array, $depth = 0) {
		$build = 'array(' . "\r\n";

		foreach ($array as $key => $value) {
			$build .= str_repeat("\t", $depth + 1) . "'" . $key . "' => ";
			if (is_integer($value)) {
				$build .= "$value";
			} else if (is_string($value)) {
				$build .= "'" . $value . "'";
			} else if (is_bool($value)) {
				$build .= $value ? "true":"false";
			}else if (is_array($value)) {
				$build .= arrayToPhpArrayCode($value, $depth + 1);
			}

			$build .= ",\r\n";
		}

		$build = substr($build, 0, strlen($build) - 2);

		$build .= "\r\n" . str_repeat("\t", $depth) . ')';

		return $build;
	}
	
	/**
	 * retourne dans un tableau la liste des fichiers contenu dans un rep
	 * Attention : un rep est aussi un fichier sous unix : il est donc pris avec
	 *
	 * @param string $dir_path
	 * @return array : list des fichier contenu dans le rep $dir_path
	 */
	function getDirFiles($dir_path) {
		$result = array();
		$dir_handle = opendir($dir_path);
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				$result[] = $file;
			}
		}
		closedir($dir_handle);
		return $result;
	}

    /**
	 * Permet d'ajouter un dir à parcourir pour des fichiers de type $this->input_files_extensions à compresser
	 *
	 * @param string $dir_path
	 * @param string extention
	 */
	function addDir($dir_path,$extension) {
        $file = array();
		//recupération des fichiers contenu dans le dir
        $contents = getDirFiles($dir_path);
	    //tri dans l'ordre le tableau pour ordonnées les fichiers
		asort($contents);

		foreach ($contents as $content) {
			if ($content != '.svn') {
				$file_path = $dir_path . '/' . $content;
				$pathinfo = pathinfo($file_path);
                if (isset($pathinfo['extension']) && $pathinfo['extension'] == $extension) {
                    $file[] = $file_path;
                }
			}
		}
        return $file;
	}

	/**
	 * Génère un mot de passe de manière aléatoire
	 *
	 * @param integer $nb_car nb de caractère du mot de passe souhaité
	 * @return string : mot de passe
	 */
	function generatePasswd($nb_car = 10){
		$chaine = 'azertyuiopqsdfghjklmwxcvbn123456789AZERTYUIOPMLKJHGFDSQWXCVBN?.,=$*@[]{}#&"+';	
    	$nb_lettres = strlen($chaine) - 1;
    	$generation = '';
	    for($i=0; $i < $nb_car; $i++){
	        $pos = mt_rand(0, $nb_lettres);
	        $car = $chaine[$pos];
	        $generation .= $car;
	    }
    	return $generation;
	}

    /**
     *
     * @param string $link
     */
    function refreshTo($link = null) {
        $url = ($link === null) ? HTTP .'index.php' : $link;
        header("Location: $url");
        exit();
    }

    /**
     *
     * @return string
     */
    function getOs() {
        if(!isset($_SERVER["HTTP_USER_AGENT"])) {
            return 'win';
        }
        if (preg_match('/Linux/i', $_SERVER["HTTP_USER_AGENT"])) {
                return 'linux';
        } else if ( preg_match('/WinNT/i', $_SERVER["HTTP_USER_AGENT"])||
                        preg_match('/Windows NT/i', $_SERVER["HTTP_USER_AGENT"]) ||
                        preg_match('/Windows 98/i', $_SERVER["HTTP_USER_AGENT"]) ||
                        preg_match('/Windows 95/i', $_SERVER["HTTP_USER_AGENT"]) ) {
            return 'win';
        } else if (preg_match('/Macintosh/i', $_SERVER["HTTP_USER_AGENT"]) || preg_match('/Mac_PowerPC/i', $_SERVER["HTTP_USER_AGENT"])) {
            return 'mac';
        } else {
                return 'win';
        }
    }
    
    /**
     * Récupère une entrée d'un tableau ou la valeur par défaut définie.
     *
     * @param array $source Le tableau dans lequel rechercher la valeur.
     * @param string $name Le nom du paramètre à récupérer.
     * @param mixed  $default_value La valeur à retourner si le paramètre n'est pas définit.
     * @return mixed Cette fonction retourne la valeur du paramètre demandé si il est définit dans le tableau, sinon elle retourne la valeur par défaut.
     */
    function getParameterFrom($source, $name, $default_value = null){
        $array = strlen($name) > 2 && substr($name, -2) == '[]';
        if ($array) {
            $name = substr($name, 0, -2);
        }

        if($source && isset($source[$name]) && $source[$name] !== null) {
            $default_value = $source[$name];
        }

        if ($default_value && $array && !is_array($default_value)) {
            $default_value = array($default_value);
        }

        return $default_value;
    }

    function sendHttpStatusCode($code) {
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
        if($code >=400 ) {
            exit();
        }
    }
?>