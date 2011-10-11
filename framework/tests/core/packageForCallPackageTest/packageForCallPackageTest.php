<?php
/**
 * Test class for the core.controler package
 * @tutorial : To test protected methods from the class controler, we need to make
 * ridges and re declare new public functions or variables
 */
require_once __DIR__.'/packageForCallPackageTest_view.php';
require_once __DIR__.'/packageForCallPackageTest_model.php';

class packageForCallPackageTest extends controler{

    public $_load_getModel_test =array();
    public $package_name_test = null;
    public $view_test         = null;
    public $model_test        = null;
    public $observer_test     = null;

    public function __construct() {
        //$this->_load_getModel_test = $this->array(); : TODO
        $this->package_name = 'packageForCallPackageTest';
        $this->view = new packageForCallPackageTest_view();
        $this->model_test        = $this->model;
        $this->observer_test     = $this->observer;
    }

    protected function _rights() {
        return true;
	}
    public function home() {
        $this->getView()->home();
    }
}
?>