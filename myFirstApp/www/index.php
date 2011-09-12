<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Point d'entrée de toute l'application.
 * Un certain nombre de librairies sont loadées : fichier de configuration et config du framework
 *
 * Les actions sont annalysée par l'observer.
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package aplication.bootStrap
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

/**
 * We need to set up a time zone. So by default it's Paris :)
 * @todo dev a simple function in accordance with the config.ini to setup a specific time zone
 */
date_default_timezone_set("Europe/Paris");

/**
 * Define the root dir file
 */
$root_www_path = dirname(__FILE__);
$root_app_path = $root_www_path . '/./..';
$framework_path = $root_app_path . "/./../framework";

/**
 * Load the config file : config.ini
 */
require_once($framework_path . '/core/lib/config.php');
config::addConfigFile($root_app_path . '/config/config.ini');

/**
 * Load the boot strap of the framework
 */
require_once($framework_path . '/bootStrap.php');

/**
 * Lets go ....
 * On y va ....
 */

try {

    ## Http ; Cookies
    define('DOMAINE_NAME', getConfig('vhost','topdomaine'));
    define('WEBMESTRES_SESSION_COOKIE_DOMAIN', '.' . DOMAINE_NAME);
    define('HTTP',getConfig('vhost','normal_protocol') . getConfig('vhost','url') . '/');
    define('HTTPS',getConfig('vhost','secure_protocol') . getConfig('vhost','url') . '/');

    //Durée d'une heure en secondes
    define('COOKIE_EXPIRE_HOUR', 3600);
    //Durée d'une journée en secondes
    define('COOKIE_EXPIRE_DAY', 86400); // COOKIE_EXPIRE_HOUR * 24
    //Durée d'un mois de 30 jours en secondes
    define('COOKIE_EXPIRE_MONTH', 2592000); // COOKIE_EXPIRE_DAY * 30
    //Durée d'une année de 365 jours en secondes
    define('COOKIE_EXPIRE_YEAR', 31536000); // COOKIE_EXPIRE_DAY * 365

	## Definition for the language : we are using Gettext and an alias function __()
    ## gestion des locales : langue
    define('ROOT_APP_PATH_LANG',ROOT_APP_PATH . '/locale/');
    define('DEFAULT_LANG', 'fr_FR');

	## Meta par defaut
    define('META_ROBOT_TITLE', getConfig('meta','title'));
    define('META_ROBOT_KEYWORD', getConfig('meta','keyword'));
    define('META_ROBOT_DESCRIPTION', getConfig('meta','description'));

    ## Definition of the css and js path files : note that the framework have got a function to minify those files and build the output file
	## nom des fichiers css & js ainsi que leur localisation
    define('USER_RELATIVE_PATH_JS', '/medias/js/');
    //define('JS_USER_FILE_NAME', getConfig('js','description'));
    define('DIR_USER_JS_FILES', USER_RELATIVE_PATH_JS . getConfig('jsfilename','dirname'));

    define('USER_RELATIVE_PATH_CSS', '/medias/css/');
    //define('CSS_USER_FILE_NAME', $css_user_file_name);
    define('DIR_USER_CSS_FILES', USER_RELATIVE_PATH_CSS . getConfig('cssfilename','dirname'));


    /**
     * Load the session class of the framework
     */
    require(USER_CORE_PATH . '/user_session.php');
    $GLOBALS['session'] = new user_session();

    /**
     * We need to use good stuff :)
     */
    require(ROOT_FRAMEWORK_PATH . '/core/lib/util_functions.php');

    /**
     * There we are. We are loading all the framework class. See that we use the core user's class. In those class you update as you whant the rooting,
     * make a general template in PHP...
     *
     * On va charger toutes les class définit pour votre application qui surcharge le core framework. Cela vous permet entre autre de définir
     * un template global au niveau de la vue. Mais vous pouvez redéfinir le comportement général du framework en fonction de vos besoins.
     */
	require(USER_CORE_PATH . '/user_view.php');
    require(USER_CORE_PATH . '/user_model.php');
    require(USER_CORE_PATH . '/user_controler.php');
    require(USER_CORE_PATH . '/user_observer.php');

    /**
     * This object analyse the user request. It allow us to know witch ressource is call (package) and witch action the user whant
     * (action defined in the controler package)
     * Cet objet permet une analyse de la requette utilisateur. Il permet de savoir qu'elle est la ressource demandée (le package) et l'action souhaitée
     * (l'action définit dans le controleur du package)
     */
    $GLOBALS['observer'] = new user_observer();

    $GLOBALS['util_localisation'] = new lang();

    /**
     * The observer is cheking if the ressource call by the user existe. If not call an exeption
     * L'observer va controler que la ressource existe et qu'elle est utilisable par un utilisateur.
     * Dans le cas contraire cela lève une exception
     */
    //$exception = false;
    $GLOBALS['observer']->checkExisteRessource();

    /**
     * The observer says that everything are OK and we get the ressource
     */
    $_SESSION['_ressource'] = $GLOBALS['observer']->getRessource();

   
    if(getUrlParameter('logout') !== null) {
        if(isset($_SESSION['user'])){
            unset($_SESSION['user']);
        }
    }

    /**
     * We get the package name and we instanciate the controleur package and send it the observer result
     * L'observer nous permet de savoir qu'elle est la ressource demandée. Elle a été vérifiée et on l'instancie
     * On pousse aussi les informations issues de l'observer
     */
    $controler = new $_SESSION['_ressource']['_p']();
    $controler->setObserverResult($_SESSION['_ressource']);

    /**
     * Everything was done and no error throw by an exception. We display the result
     */
    echo $controler->display();

/**
 * We catch all exceptions and display or do something. You can update as you whant and for your need
 */
}catch (Exception $e) {

    if($e->getCode() == 403 || $e->getCode() == 401) {
        refreshTo('index.php');
        exit();
    }

    $GLOBALS['util_localisation'] = new lang();
    $controler = new exceptions();

    switch($e->getCode()) {
        case '500':
            $ressource = array('_p'=>'exceptions','param'=>array('_a'=>'error500','_v'=>getUrlParameter('_v')) );
            break;
        default:
            $ressource = array('_p'=>'exceptions','param'=>array('_a'=>'home','_v'=>getUrlParameter('_v')));
            break;
    }

    $controler->setObserverResult($ressource);
    echo $controler->display();
}
?>