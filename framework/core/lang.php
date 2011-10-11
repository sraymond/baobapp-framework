<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Classe de gestion de la localisation
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.lang
 * @copyright Copyright (c) 2007-2099 Stéphane Raymond
 */
class lang {
	const LANG_AUTO_DETECT = '_detect_';
	const LANG_AUTO_CHOOSE = '_choose_';
	const LANG_FR = 'fr_FR';
	const LANG_EN = 'en_US';

	/**
	 * Curent language
	 *
	 * @var string Code langue à utiliser
	 */
	private $lang = null;

	/**
	 * Constructeur par défaut
	 *
	 * @param string $lang Code de la langue à définir.
	 * Le code peut être un code standard : http://www.loc.gov/standards/iso639-2/php/code_list.php
	 * ou une des constante self::LANG_AUTO_DETECT (detection automatique à partir de la requete utilisateur)
	 * ou LANG_AUTO_CHOOSE (lecture de l'url, session ou cookie poiur trouver la langue définie,
	 * sinon auto detect, sinon valeur de set_env_conf)
	 */
	public function __construct($lang = self::LANG_AUTO_CHOOSE) {
	    $this->setLang($lang);
		bindtextdomain(getConfig('cookie','name','local'), ROOT_APP_PATH_LANG);
		bind_textdomain_codeset(getConfig('cookie','name'), 'UTF-8');
		textdomain(getConfig('cookie','name'));
	}

	/**
	 * return the language used
	 *
	 * @return string Cette fonction retorune le code langue en cours.
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * Define the language that we use during the session
	 * Le code peut être un code standard : http://www.loc.gov/standards/iso639-2/php/code_list.php
	 * ou une des constante self::LANG_AUTO_DETECT (detection automatique à partir de la requete utilisateur)
	 * ou LANG_AUTO_CHOOSE (lecture de l'url, session ou cookie poiur trouver la langue définie,
	 * sinon auto detect, sinon valeur de set_env_conf)
	 *
	 * @param string $lang Code de la langue à définir.
	 * @param string La langue définit (utile en AUTO_CHOOSE ou AUTO_DETECT)
	 */
	public function setLang($lang) {
		if ($lang == self::LANG_AUTO_CHOOSE) {
			if (
				!($lang = getUrlParameter('_lang')) &&
				!($lang = getSessionParameter('lang')) &&
				!($lang = getCookieParameter('lang'))
			) {
				$lang = self::LANG_AUTO_DETECT;
			}
		}

		if ($lang == self::LANG_AUTO_DETECT) {
			$lang = $this->autoDetectLang();
		}

		$this->lang = $this->chooseSystemLocal($lang);
		
		putenv("LANG=" . $this->lang);
		putenv("LANGUAGE=" . $this->lang);

		setlocale(LC_ALL, $this->lang);
		setlocale(LC_MESSAGES, $this->lang);
		setSessionParameter('lang', $this->lang);
		setCookieParameter('lang', $this->lang);
		return $this->lang;
	}

	/**
	 * Detec the user language
	 */
	public function autoDetectLang() {
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {

			$langs_list = array();
			$accept_langs = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);

			foreach ($accept_langs as $lang) {
				$q_pos = strchr($lang,";");
				$q = 100;
				if ($q_pos !== false) {
					$q = explode(";",$lang) ;
					$lang = $q[0] ;
					$q = (float)(array_pop(explode("=", $q[1]))) * 100;
				}

				$langs_list[$lang] = $q;
			}

			asort($langs_list);
			$langs_list = array_reverse($langs_list);

			foreach ($langs_list as $lang => $weight) {
				if (file_exists(ROOT_APP_PATH_LANG . $lang)) {
					return $lang;
				}
			}
		}

		return DEFAULT_LANG;
	}

	/**
	 * Transform a string in the corect language and replace the %key% in is equivalent
	 * Les codes %key%  sont remplacé par leur équivalent dans le tableau de paramètres.
	 * @example
	 * __('Le %number%eme élément', array('number' => 5));
	 * Affiche :
	 * - in french : Le 5eme élément
	 * - in english : The 5th element
	 *
	 * @param string $text Le texte à traduire
	 * @param array $params Les paramètres à faire correspondre
	 * @return string Cette fonction retorune un chemin valide selon la langue définie
	 */
	public function getText($text, $params = array()) {
		return nsprintfn(_($text), $params);
	}

    /**
     * Transforme a link for using the good path. For exemple if you have button use language
     * @param <type> $path
     * @return <type>
     */
	public function getPath($path) {
		return nsprintfn(
			$path,
			array('lang' => $this->getLang())
		);
	}

	private function chooseSystemLocal($lang = '') {
	    $en_lang = array('en','us','EN','US','en_us','en_US','EN_US','en-us','EN-us','EN-US');
	    $fr_lang = array('fr','FR','fr_FR','FR_fr','FR_FR','fr-fr','FR-fr','FR-FR');
	    
	    if ($lang == '') {
	    	return DEFAULT_LANG;
	    }
	    if(in_array($lang,$en_lang) ){
	        return 'en_US';
	    }
	    if(in_array($lang,$fr_lang) ){
	        return 'fr_FR';
	    }
	}
}

/**
 * Global function use in the framework for the translation
 * @example
 * __('Le %number%eme élément', array('number' => 5));
 * This string "Le %number%eme élément" is translate in the gettex file like : "the %number%th element"
 * The result is :
 * - in french : Le 5eme élément
 * - in english : The 5th element
 *
 * @param string $text Le texte à traduire
 * @param array $params Les paramètres à faire correspondre
 * @return string Cette fonction retorune un chemin valide selon la langue définie
 *
 * @uses util_localisation::getText
 */
function __($text, $params = array()) {
	return $GLOBALS['util_localisation']->getText($text, $params);
}

/**
 * Transforme a link for using the good path. For exemple if you have button use language
 *
 * @param string $path Chemin à transformer
 * @return string Cette fonction retorune un chemin valide selon la langue définie
 *
 * @uses util_localisation::getPath
 */
function _path($path) {
	return $GLOBALS['util_localisation']->getPath($path);
}
?>