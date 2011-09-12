<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * - Bootstrap to init the framework
 * - Autoload declaration
 *
 * - Initialisation des fonctions d'auto load du framework et de constantes de chemin d'application
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package bootstrap
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 * 
 */

/**
 * CONSTANT definition for all the project/aplication
 * Définition des constantes globales à un projet
 */

## Root acces file
## Constante pour définir les différents chemins de l'apli
define('ROOT_WWW_PATH',$root_www_path);
define('ROOT_APP_PATH',$root_app_path);
define('ROOT_FRAMEWORK_PATH', $framework_path);
define('USER_CORE_PATH', ROOT_APP_PATH . '/core');
define('RESSOURCE_PACKAGE_PATH', ROOT_APP_PATH . '/package');

/**
 * SPL AUTOLOAD : load the core object
 * Fonction de chargement des librairies issues du core
 * @param string $classname
 */
function framework_autoload($classname) {
    file_exists(ROOT_FRAMEWORK_PATH . '/core/'.$classname.'.php') &&
    require_once(ROOT_FRAMEWORK_PATH . '/core/'.$classname.'.php');
}
/**
 * SPL AUTOLOAD : load the core librairie object. Those librarys are only design for the Framework. You can not use them with onother one.
 * Fonction de chargement des librairies développées pour le framework. Il n'est pas possible de les utiliser dans un autre projet.
 * @param string $classname
 */
function framework_lib_autoload($classname) {
   if(file_exists(ROOT_FRAMEWORK_PATH . '/core/lib/'.$classname.'.php') ){
       require_once(ROOT_FRAMEWORK_PATH . '/core/lib/'.$classname.'.php');
   }elseif(file_exists(ROOT_FRAMEWORK_PATH . '/core/lib/'.$classname.'/'.$classname.'.php')){
       require_once(ROOT_FRAMEWORK_PATH . '/core/lib/'.$classname.'/'.$classname.'.php');
   }
}
/**
 * SPL AUTOLOAD : load the package controler. When a user send a request for gettint a ressource, the framework will be load the appropriate controler.
 * Fonction de chargement du controleur d'un package. Un package définit un métier. Au sein d'un métier, il peut y avoir plusieurs actions possibles. Le framework va loafer
 * le bon controleur en fonction de la requette utilisateur.
 * @param <string> $classname
 */
function package_autoload($classname) {
    $parse = explode('_',$classname);
    file_exists(RESSOURCE_PACKAGE_PATH . '/'.$parse[0].'/'.$classname.'.php') &&
    require_once(RESSOURCE_PACKAGE_PATH . '/'.$parse[0].'/'.$classname.'.php');
}

/**
 * SPL AUTOLOAD : Load external librarys. Those labrary are not specialy build for the framework. You can plug every object lib you whant but you need to respect some conventions
 * Fonction de chargement des librairies externe au framework. Ces libraires doivent etre de type objets et respecter certaines conventions de nomage pour
 * être utilisées au sein du framework et donc de votre application
 * @param string $classname
 */
function framework_package_external_lib_autoload($classname) {
    if(file_exists(ROOT_FRAMEWORK_PATH . '/core/lib/external/'.$classname.'.php') ){
       require_once(ROOT_FRAMEWORK_PATH . '/core/lib/external/'.$classname.'.php');
    }elseif(file_exists(ROOT_FRAMEWORK_PATH . '/core/lib/external/'.$classname.'/'.$classname.'.php')){
       require_once(ROOT_FRAMEWORK_PATH . '/core/lib/external/'.$classname.'/'.$classname.'.php');
    }
}

spl_autoload_register('framework_autoload');

spl_autoload_register('package_autoload');

spl_autoload_register('framework_lib_autoload');

spl_autoload_register('framework_package_external_lib_autoload');


/**
 * Activate Xdebug. Do not forget to compile the Xdebug source and at it to the php extension dir file (see the phpinfo() to get the root dir file)
 * Permet d'activer Xdebug. N'oubliez pas de compiler Xdebug et de rajouter l'extension dans le dossier (voir le phpinfo() pour connaitre le dossier des extensions)
 *
 * @param string $active : on - off
 * @return void
 */
function XDebugProfiler($active) {
    
	switch($active) {
		case 'on':
            error_reporting(E_NOTICE);
			xdebug_enable();
			ini_set('xdebug.show_local_vars',1);			
			error_reporting();
		    ini_set('display_errors',1);
		    break;
		default:
			if (xdebug_is_enabled()) {
				xdebug_disable();
				ini_set('xdebug.show_local_vars',0);
				ini_set('xdebug.profiler_enable',0);
				ini_set('xdebug.profiler_append',0);
			} 
			ini_set('display errors',0);
			break;	
	}
    
}
if(getConfig('debug','off') == 'on' && (!isset($phpunit) || $phpunit != true)) {
	XDebugProfiler('on');
}

/**
 * This is use for phpunit test. When you are going to test the application, you will use a different DB. This
 * variable will be use to get the good BDD config file for your unit test.
 *
 * Cette variable est utilisé pour les tests unitaires. Elle sera utile pour loader le fichier de configuration de la base de données. Ainsi vous pouvez avoir un jeu de test
 * indépendant de votre base de données de dev ou de preproduction.
 */
if(!isset($phpunit)) {
	global $phpunit;
	$phpunit = false;
}

?>