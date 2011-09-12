<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

abstract class core {

    /**
     *
     * @var array : save all object still on durig the execution of the script
     */
    private $_package_load =array();

    /**
	 * This methode allow a view to make a transversal call to get back the result of this call from an other view
	 *
	 * @param string $package_name Name of the package call / Nom du package à appeler
	 * @param string $action Action to performe / Nom de l'action à appeler
	 * @param array $get Simulation of the $_GET parameter / Paramètres $_GET simulé
	 * @param array $post Simulation of the $_POST parameter / Paramètres $_POST simulé
	 *
	 * @return string html code : the result of the view / Code HTML généré par le package appelé
	 */
    final protected function callPackage($package_name,$action = 'home', $get = array(), $post = array(),$force = false) {
        $param = array('_a'=>$action,'_GET'=>$get,'_POST'=>$post);
        if(isset($this->_package_load[$package_name]) && $force === false) {
            return $this->_package_load[$package_name]->display(true,$param);
        }
        $this->checkCallPackage($package_name);
        require_once(RESSOURCE_PACKAGE_PATH . '/' . $package_name . '/' . $package_name . '.php');
        $this->_package_load[$package_name] = new $package_name ();
        return $this->_package_load[$package_name]->display(true,$param);
    }

    /**
	 * Return the list of methodes allow to be call by a controler : check if the action existe / Liste de méthodes autorisées pour le package courant.
	 *
	 * @return array
	 */
	protected function getAllowedMethods() {
		$reflector = new ReflectionClass($this->package_name);
		$methods = $reflector->getMethods();
		foreach ($methods as $k => $v) {
			$methods[$k] = $v->name;
		}

		$reflector = new ReflectionClass('controler');
		$methods2 = $reflector->getMethods();
		foreach ($methods2 as $k => $v) {
			if ($v->isAbstract()) {
				unset($methods2[$k]);
			} else {
				$methods2[$k] = $v->name;
			}
		}
		return array_diff($methods, $methods2);
	}

    /**
	 * Check that the package call existe
	 *
	 * @param string $package_name
	 * throw a new exception
	 */
    final protected function checkCallPackage($package_name) {
        $parse = explode('_',$package_name);
        if (file_exists(RESSOURCE_PACKAGE_PATH . '/' . $parse[0] . '/' . $package_name . '.php') !== true) {
            throw new Exception(_('La ressource '.$package_name.' demandée du package appelé n\'existe pas.'), 500);
        }
    }
}
?>