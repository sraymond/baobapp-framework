<?php
/**
 * Test class for the core.controler package
 * @tutorial : To test protected methods from the class controler, we need to make
 * ridges and re declare new public functions or variables
 */
class controlerTest extends controler{

    public $_load_getModel_test =array();
    public $package_name_test = null;
    public $view_test         = null;
    public $model_test        = null;
    public $observer_test     = null;

    public function __construct() {
        //$this->_load_getModel_test = $this->array(); : TODO
        $this->package_name = 'controlerTest';
        $this->view = new controlerTest_view();
        $this->model_test        = $this->model;
        $this->observer_test     = $this->observer;
    }

    public function homeTest($mode = 'second') {
        return $this->home($mode);
    }
    protected function home($mode = 'second'){
        if ($mode == 'second'){
            $this->getView()->testViewActionCall('output');
        }else {
            return true;
        }
    }

    public function _rightsTest() {
        return $this->_rights();
    }
	protected function _rights(){
        return true;
    }
    public function checkRightTest($action) {
        return self::checkRight($action);
    }

    /**
     * methode created for the test case
     */
    public function testActionAlone(){
        return true;
    }

    public function testAction() {
        $this->getView()->testViewActionCall('output');
    }

    public function testForceAction(){
        $this->getView()->testViewForceActionCall();
    }
    

    /**
     *
     * @param <type> $name
     * @return <type>
     */
    public function workflowChangePackageName($name) {
        $this->setPackageName($name);
        $this->package_name_test = $this->package_name;
        return $this->package_name_test;
    }

    public function testGetView() {
        return $this->getView();
    }

    public function testGetModel($mode = '') {
        switch ($mode) {
            case 'controlerTestSecond':
                return $this->getModel('controlerTestSecond');
                break;
            case 'controlerTestSecondForced':
                return $this->getModel('controlerTestSecond',true);
                break;
            default:
                return $this->getModel();
                break;
        }
       
    }

    public function testCallPackage($package_name,$action = '',$get = array(), $post = array(),$force = false) {
        if ($action == '') {
            return $this->callPackage($package_name);
        }
        return $this->callPackage($package_name,$action,$get,$post,$force);
    }

    
}
?>