<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.view
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

/**
 * We do not use the spl autoload to improuve performance
 * The meta lib is linked to the the framework and it is not an external lib
 */
//require_once(ROOT_FRAMEWORK_PATH . '/core/lib/meta.php');

abstract class view extends core {
    
    protected $mode;

    protected $relative_path_js;
    protected $js_file_name;
    protected $dir_user_js_file_name;
	
    protected $relative_path_css;
    protected $css_file_name;
    protected $dir_user_css_file_name;
    
    private $list_css_file = array();
    private $list_js_file = array();
    
    protected $jsonVariables = array();
	
    protected $output = '';

    protected $list_message = array();
    
	function __construct() {
        $this->relative_path_js = '/medias/js/';
        $this->js_file_name = getConfig('jsfilename','outputName');
        $this->dir_user_js_file_name = getConfig('jsfilename','dirname');

        $this->relative_path_css = '/medias/css/';
        $this->css_file_name = getConfig('cssfilename','outputName');
        $this->dir_user_css_file_name = getConfig('cssfilename','dirname');
    }

    abstract protected function header();
    
    abstract protected function footer();   

    abstract protected function generateCssContent();

    abstract protected function generateJsContent();

	/**
	 * Ajoute un code à la sortie HTML
	 *
	 * @param visualComponent|string $output Coded ou composant à ajouter en sortie.
	 */
	public function output($output) {
		if (!is_string($output)) {
			$output = $output->toString();
		}
		$this->output .= $output;
	}

	/**
	 * Vide la sortie HTML
	 */
	public function resetOutput() {
		$this->output = '';
	}
	
	/**
     * Return the html code : use by the controler (see the bootstrap of you app echo $controler->display();)
     *
     * @param bolean $call_package : a package view can call a controleur of another package.
     * The result is that this controleur just send back the result of this view.
     *
     * @return string html code
     */
	public function toString($call_package = false) {
        //appel par un call package : mode "box"
        if($call_package === true) {
            $html = $this->output;
            $this->resetOutput();
            return $html;
        }
        
		$html = $this->header() .
                $this->output .
                $this->footer();

        $this->resetOutput();
        return $html;
	}

    /**
     * Whant to save a message if something was rong (a user input control). Just add a message with a type into an array
     * @param string $message
     * @param string $type
     */
    public function setMessage($message,$type) {
        $this->list_message[$type][] = $message;
    }

    /**
     * Check if a type message exist
     * @param <string> $type
     * @return <boolean>
     */
    public function isATypeMessageExist($type) {
        if(isset($this->list_message[$type]) && count($this->list_message[$type]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Generate meta tag and use the lib meta in the core/lib
     *
     * @return $string meta tag
     */
    protected function meta() {
        $meta_robot = new meta();
        $meta_robot->GenerateMeta();
        
        return $meta_robot->GetFinalMeta();
    }

    /**
     * Encode an array in a JSON format
     * @return string JSON format
     */
    protected function jsonOutput() {
        return json_encode($this->jsonVariables);
    }

    /**
     * Add in an array somme key value to be encode in JSON format
     * @param mixed $name
     * @param mixed $value
     */
    protected function setJsonInput($name,$value) {
        $this->jsonVariables[$name] = $value;
    }
     
    /**
     * allow you to create a compact css file
     *
     * @param string $output_file_name :
     */
    protected function createCompactZipCss() {
       setlocale(LC_ALL,"C");
       require_once(ROOT_FRAMEWORK_PATH . '/core/lib/external/csstidy/class.csstidy.php'); 
       require_once(ROOT_FRAMEWORK_PATH . '/core/lib/concatFiles/cssConcat.php');
       
       $cssConcat = new cssConcat(ROOT_WWW_PATH . $this->relative_path_css . getConfig('cssfilename','outputName'));
       $cssConcat->addDir( ROOT_WWW_PATH . $this->relative_path_css . $this->dir_user_css_file_name . '/' . getConfig('cssfilename','dirname'), true);
       	   
	   $cssConcat->compute(); 
    }
    
    /**
     * allow you to create a compact js file. WARNING : do not add a tiny js file !
     *
     * @param string $output_file_name
     */
    protected function createCompactZipJs() {
        //setlocale(LC_ALL,"C");
        require_once(ROOT_FRAMEWORK_PATH . '/core/lib/external/jsmin-1.1.1.php');
        require_once(ROOT_FRAMEWORK_PATH . '/core/lib/concatFiles/jsConcat.php');
        setlocale(LC_ALL,"C");

        $jsConcat = new jsConcat(ROOT_WWW_PATH . $this->relative_path_js . getConfig('jsfilename','outputName'));
        $jsConcat->addDir( ROOT_WWW_PATH . $this->relative_path_js . $this->dir_user_js_file_name . '/' . getConfig('jsfilename','dirname'), true);
        $jsConcat->compute(); 
    }
}
?>