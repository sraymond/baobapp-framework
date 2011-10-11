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
     * save all object still on durig the execution of the script
     *
     * Garde en mémoire les objets instanciés durant l'exécution du script
     *
     * @var array
     */
    private $_package_load =array();

    /**
	 * This is a transversal call get a partial content from an action. You get the result without the layout.
     *
     * Permet de faire un appel à un package au sein du package courant. Cela permet
     * de récupérer du contenu partiel d'une action sans l'abillage (Layout)
     * 
	 * @param string $package_name Name of the package call / Nom du package à appeler
	 * @param string $action Action to performe / Nom de l'action à appeler
	 * @param array $get Simulation of the $_GET parameter / Paramètres $_GET simulé
	 * @param array $post Simulation of the $_POST parameter / Paramètres $_POST simulé
	 *
	 * @return string html code : the result of the view / Code HTML généré par le package appelé
	 */
    final protected function callPackage($package_name,$action = 'home', $get = array(), $post = array(),$force = false) {
        
        //this object exist so we return it
        $param = array('_a'=>$action,'_GET'=>$get,'_POST'=>$post);
        if(isset($this->_package_load[$package_name]) && $force === false) {
            return $this->_package_load[$package_name]->display(true,$param);
        }

        //the package does not exist or we want to reload it ($force === true)
        try{
            $this->checkCallPackage($package_name);
            require_once(RESSOURCE_PACKAGE_PATH . '/' . $package_name . '/' . $package_name . '.php');
            $this->_package_load[$package_name] = new $package_name ();
        } catch (Exception $e) {
            if(class_exists($package_name,false)) {
                $this->_package_load[$package_name] = new $package_name ();
            }else{
                throw new Exception($e->getMessage(), 500);
            }
        }
        return $this->_package_load[$package_name]->display(true,$param);
    }

    /**
	 * Return an array contain all methodes exists in the current object and allowed  to be execute.
     *
     * Retourne dans un tableau les méthodes qui existe au sein de l'objet et qui sont exécutables.
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
	 * Check the package exist
     *
     * Contrôle que le package existe (au sens fichier)
	 *
	 * @param string $package_name
     * @return throw a new exception if the package does not exist
	 */
    final protected function checkCallPackage($package_name) {
        $parse = explode('_',$package_name);
        if (file_exists(RESSOURCE_PACKAGE_PATH . '/' . $parse[0] . '/' . $package_name . '.php') !== true) {
            throw new Exception(_('The class "'.$package_name.'" does not exist'), 500);
        }
    }
}
?>