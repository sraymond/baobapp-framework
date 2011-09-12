<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Management of ini configuration files
 *
 */
class configFile {
	/**
	 * Config values for this file. Stored under a bidimensional array of the form : $config[section][item] = value
	 * @var array
	 */
	private $config = null;
	
	/**
	 * Path of the file (null in write mode)
	 * @var string
	 */
	private $path = null;

	/**
	 * Default constructor
	 *
	 * @param string $path The file to load. Must be set to null in write only mode
	 */
	public function __construct($path = null) {
		if ($path) {
			$this->load($path);
		}
	}

	/**
	 * Load and parse a given file
	 *
	 * @param string $path
	 * @throws Exception if File not found or parsing fail
	 */
	private function load($path) {
		if (!$this->config) {
			$this->path = $path;
			if (!$this->config = parse_ini_file($path, true)) {
				throw new Exception('Can\'t parse file ' . $path, 500);
			}
		}
	}

	/**
	 * Get The value of a section/item
	 *
	 * @param string $section
	 * @param string $item
	 * @param string $default_value The value to set if the section/item doesn't exists
	 * @return mixed
	 */
	public function getConfig($section, $item, $default_value = null) {
		if (isset($this->config[$section]) && isset($this->config[$section][$item])) {
			return $this->config[$section][$item];
		}
		return $default_value;
	}

	/**
	 * Add or update a config value
	 *
	 * @param string $section
	 * @param string $item
	 * @param mixed $value
	 */
	public function setConfig($section, $item, $value) {
		if (!isset($this->config[$section])) {
			$this->config[$section] = array();
		}
		$this->config[$section][$item] = $value;
	}

	/**
	 * Set a complete set of config items
	 *
	 * @param array $array An array of items of the form config[section][item] = value
	 */
	public function setConfigs($array) {
		foreach ($array as $section => $items) {
			if (!is_array($items)) {
				$this->setConfig('', $section, $item);
			} else {
				foreach ($items as $item => $value) {
					$this->setConfig($section, $item, $value);
				}
			}
		}
	}
}

/**
 * Config factory used to managge multiple files and to access config in a static way
 */
abstract class config {
	/** Index of the dummy file used for forced values. */
	const FORCED_FILE_INDEX = 'forced_values';

	/**
	 * List of config files loaded + forced dummy file
	 *
	 * @var array
	 */
	private static $configs = null;

	/**
	 * Convert a string defining a data size using markers like K, M, G into a value in bytes
	 *
	 * @param string $val
	 * @return int
	 */
	public static function string2bytes($val) {
		$val = trim($val);

		if (strlen($val) == 0) {
			return null;
		}

	    $last = strtolower($val{strlen($val)-1});

		switch($last) {
	        // Le modifieur 'G' est disponible depuis PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }

	    return $val;
	}

	/**
	 * Add another config file to the factory
	 *
	 * @param string $file_path
	 */
	public static function addConfigFile($file_path) {
		if (!self::$configs) {
			self::reset();
		}
		self::$configs[] = new configFile($file_path);
	}

	/**
	 * Get a config value by its section/item
	 *
	 * @param string $section
	 * @param string $item
	 * @param mixed $default_value Value to set if the item doesn't exists
	 * @return mixed
	 * @throws Exception if no config file loaded
	 */
	public static function getConfig($section, $item, $default_value = null) {
		// Valeurs forcées par le code
		if (!self::$configs) {
            return $default_value;
			//throw new Exception('No configuration file loaded.', 404);
		}

		// Recherche de l'entrée.
		foreach (self::$configs as $conf) {
			if (($result = $conf->getConfig($section, $item)) !== null) {
				return $result;
			}
		}
		return $default_value;
	}

	/**
	 * Force a config value. Once its was forced, it will be return instead of any file defined values for the same section/item
	 *
	 * @param string $section
	 * @param string $item
	 * @param mixed $value
	 */
	public static function forceConfig($section, $item, $value) {
		if (!self::$configs) {
			self::reset();
		}
		self::$configs[self::FORCED_FILE_INDEX]->setConfig($section, $item, $value);
	}

	/**
	 * Write or replace an ini file using the defined array and path.
	 *
	 * @param string $path
	 * @param unknown_type $array An array of items of the form config[section][item] = value
	 */
	public static function writeIniFile($path, $array) {
		$config_file = new configFile();
		$config_file->setConfigs($array);
		$config_file->save($path);
	}

	/**
	 * Reload all files
	 */
	public static function reload() {
		foreach (self::$configs as $k => $conf) {
			if ($k != self::FORCED_FILE_INDEX) {
				$conf->reload();
			}
		}
	}

	/**
	 * Reset the factory by removing all loaded configs
	 */
	public static function reset() {
		self::$configs = array(self::FORCED_FILE_INDEX => new configFile());
	}
}



/**
 * Quick function to ask a value to the factory. Used to ease developpement
 *
 * @param string $section
 * @param string $item
 * @param mixed $default_value
 * @return mixed
 */
function getConfig($section, $item, $default_value = null) {
	return config::getConfig($section, $item, $default_value);
}

?>