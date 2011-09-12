<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.controler
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */
abstract class controler extends core {

    /**
     *
     * @var array : save class
     */
    private $_load_getModel =array();

    /**
	 * the curent package name
	 * @var string
	 */
	protected $package_name = null;
	
	/**
	 * view class of the curent package call
	 * @var view
	 */
	protected $view         = null;
	
	/**
	 * model class of the curent package call
	 * @var model
	 */
	protected $model         = null;

	/**
	 * observer object injected
	 * @object $observer
	 */
	protected $observer = null;

	/**
     * Instanciate the view and the model of the curent package
     */
	public function __construct() {
        if (
            file_exists(RESSOURCE_PACKAGE_PATH . '/'.$this->package_name.'/'.$this->package_name.'_view.php') !== true || 
            file_exists(RESSOURCE_PACKAGE_PATH . '/'.$this->package_name.'/'.$this->package_name.'_model.php') !== true ) {
            throw new Exception(__('Pour utiliser le package "'.$this->package_name.'" : la vue et le model doivent avoir été déclaré au préalable même si ils n\'ont aucune méthodes.'), 500);
        }
        $this->view = $this->_instanciateObject($this->package_name . '_view');
        $this->model = $this->_instanciateObject($this->package_name . '_model');
    }

	/**
	 * All package need to have a home action and a view asociated
	 */
	abstract protected function home();

	/**
	 * You need to define a right rule in each package controler. But the rule it's yours ;-).
     * In the package exemple you can fing one of them
	 * @return array Cette fonction retourne un tableau de droits action => droits
	 */
	abstract protected function _rights();
	
	/**
     * decide how the user can have an access to this action. You can over write in the user_controler if you whant or just use this simple one
	 * Le controle d'accès peut être redéfinit dans user_controler
     * @param string $action
     * @return boolean true by default : you have full access to the action
     */
    protected function checkRight($action) {
        return true;
    }
	
	/**
     * Check the right for an user access, then check if the method call exist and then execute the action and get the result from the view and send it back
     * to the boot strap for the final eco
     * @param boolean $call_package
     * @param string $call_package_param
     * @return mixed : throw exception Or return an html string
     */
	public function display($call_package = false,$call_package_param = array()) {
        
	    if($call_package === true) {
            $action = $call_package_param['_a'];
            $this->_allocatePostGetVariable($call_package_param);
        }else {
            $action = $this->observer['param']['_a'];
        }

		$this->checkRight($action);
        
        $methods = $this->getAllowedMethods();
			
		if (in_array($action, $methods,true) !== true) {
            throw new Exception(__('Action inexistante (%package%:%action%)',array('package' => $this->package_name, 'action' => $action)), 500);
        }
        
        $this->$action();

        $result = ($this->view ? $this->view->toString($call_package) : '!');
		return $result;	
	}

    /**
     * Init the observer result in the class
     * @param string $ressource
     */
    public function setObserverResult($ressource) {
        $this->observer = $ressource;
    }
    
    /**
     * Return the view of the curent package
     * @return object
     */
	final protected function getView() {
        return $this->view;
	}

    /**
     * Return the model object of the curent package
     * @return object
     */
	protected function getModel() {
		return $this->model;
	}

    /**
     * Allow you to call an other model from others package
     * @param string $package_name
     * @param boolean $force
     * @param array $param
     * @return object
     */
    protected function _getModel($package_name,$force = false,$param = '') {
        if(isset($this->_load_getModel[$package_name]) && $force === false) {
            return $this->_load_getModel[$package_name];
        }
        $this->_load_getModel[$package_name] = $this->_instanciateObject($package_name . '_model',$param);
        return $this->_load_getModel[$package_name];
    }

    /**
     * Instanciate an object
     * @param string $name
     * @param string $param
     * @return object
     */
    private function _instanciateObject($name,$param = '') {
        if($param !='') {
            return new $name($param);
        }
        
        return new $name();
    }

    /**
     * Allow to push in $_POST and $_GET somme key value when using call_package : simulation
     * @param array $param
     */
    private function _allocatePostGetVariable($param) {
        if(is_array($param['_GET']) && count($param['_GET']) > 0) {
            foreach ($param['_GET'] as $clef => $value) {
                if(isset($_GET[$clef])) {
                    $_GET['package_' . $clef] = $value;
			    }else {
                    $_GET[$clef] = $value;
			    }
			}
	    }
		if(is_array($param['_POST']) && count($param['_POST']) > 0) {
            foreach ($param['_POST'] as $clef => $value) {
                if(isset($_POST[$clef])) {
                    $_POST['package_' . $clef] = $value;
			    }else {
                    $_POST[$clef] = $value;
			    }
			}
		}
    }
}
?>