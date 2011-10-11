<?php
namespace tests\units;

define('RESSOURCE_PACKAGE_PATH','./core/');
define('ROOT_FRAMEWORK_PATH','./../');

require_once __DIR__.'/../core/core.php';

require_once __DIR__.'/../core/controler.php';
require_once __DIR__.'/core/controlerTest.php';

require_once __DIR__.'/../core/view.php';
require_once __DIR__.'/core/viewTest.php';
require_once __DIR__.'/core/controlerTest_view.php';

require_once __DIR__.'/../core/model.php';
require_once __DIR__.'/core/modelTest.php';
require_once __DIR__.'/core/controlerTest_model.php';

require_once __DIR__.'/../core/lib/external/mageekguy.atoum.phar';

use \mageekguy\atoum;


/**
 * The controler is an entry point. We are going to test the controler framework core
 * and part of the model & view.
 */
class controlerTest extends atoum\test
{

    /**
     * Rights
     */
    protected function home() {
        return true;
    }
	protected function _rights() {
        return true;
    }
    public function testHome () {
        $test = new \controlerTest();
        $this->assert
            ->boolean($test->homeTest('first') )
            ->isTrue();
    }
    public function testRights () {
        $test = new \controlerTest();
        $this->assert
            ->boolean($test->_rightsTest() )
            ->isTrue();
    }
    public function testCheckRight () {
        $test = new \controlerTest();
        $action = 'test';
        $this->assert
            ->boolean($test->checkRightTest($action) )
            ->isTrue();

        $action = 'test2';
        $this->assert
            ->boolean($test->checkRightTest($action) )
            ->isTrue();
    }


    /**
     * Package
     */
    public function testSetPackageName() {
        $test = new \controlerTest();
        $test->setPackageName('test');

        $this->assert->string($test->getCurentPackageName())->isEqualTo('test');
        $this->assert->variable($test->package_name_test)->isNull();
    }
    public function testWorkflowChangePackageName() {
        $test = new \controlerTest();
        $test->workflowChangePackageName('test');

        $this->assert->string($test->getCurentPackageName())->isEqualTo('test');
        $this->assert->variable($test->package_name_test)->isEqualTo('test');
    }
    public function testViewVariable() {
        $test = new \controlerTest();
        $this->assert->variable($test->view_test)->isNull();
    }
    public function testModelVariable() {
        $test = new \controlerTest();
        $this->assert->variable($test->model_test)->isNull();
    }

    
    /**
     * Observer
     */
    public function testObserverVariable() {
        $test = new \controlerTest();
        $this->assert->variable($test->observer_test)->isNull();
    }
    public function testSetObserverResult() {
        $test = new \controlerTest();
        $val['param'] = array('_p'=>'test','_a'=>'testAction');
        $test->setObserverResult($val);
        $this->assert->array($test->getObserverResult())->isNotEmpty();
    }

    
    /**
     * Action
     */
    public function testTestAction() {
        $test = new \controlerTest();
        $this->assert
            ->boolean($test->testActionAlone() )
            ->isTrue();
    }


    /**
     * Test init call view for controler test
     */
    public function testTestGetView() {
        $test = new \controlerTest();
        
        $val['param'] = array('_p'=>'controlerTest','_a'=>'testActionAlone');
        $test->setObserverResult($val);
        $test->setPackageName($val['param']['_p']);
        $test->setView();
        $this->assert
            ->object($test->testGetView() )
            ->isInstanceOf('view');
    }
     

    /**
     * Display
     */
    public function testDisplay() {
        $test = new \controlerTest();
        
        //Case 1: The view object is not call from the action or the view is not instanciate
        $val['param'] = array('_p'=>'controlerTest','_a'=>'testActionAlone');
        $test->setObserverResult($val);
        $test->setPackageName($val['param']['_p']);
        $this->assert
                ->string($test->display())
                ->isNotEmpty()
                ->isEqualTo('headerfooter');

        //Case 2: the action call does not exist : throw a new Exception
        $val['param']['_a']='actionNonExist';
        $test->setObserverResult($val);
        $this->assert
            ->exception(function() use ($test) {
                    $test->display();
                })
                ->isInstanceOf('\Exception')
				->hasMessage('This action "'.$val['param']['_a'].'" does not exist for the package "'.$val['param']['_p'].'"')
                ;

        #init the class view for case 3 & 4
        $test->setView();
        
        //Case 3: instanciate the view object and return a string
        $val['param']['_a']='testAction';
        $test->setObserverResult($val);
        $this->assert
                ->string($test->display())
                ->isNotEmpty()
                ->isEqualTo('header content_view footer');

        //Case 4: This is the special call : just return the content of the view call with out the layout
        $test->setObserverResult($val);
        $this->assert
                ->string($test->display(true,array('_a'=>'testForceAction','_GET'=>'','_POST'=>'')))
                ->isNotEmpty()
                ->isEqualTo('force_content_view');
    }


    /**
     * Model
     */
    public function testModel() {
        $test = new \controlerTest();
        $val['param'] = array('_p'=>'controlerTest','_a'=>'testActionAlone');
        $test->setObserverResult($val);
        $test->setPackageName($val['param']['_p']);
        
        //Case 1 : check the curent model package
        $this->assert
            ->object($test->testGetModel() )
            ->isInstanceOf('model');
        $test->removeModel('controlerTest');

        $test->setModel();
        $this->assert
            ->object($test->testGetModel() )
            ->isInstanceOf('model');
        $test->removeModel('controlerTest');

        $this->assert
            ->object($test->testGetModel('controlerTestSecondForced') )
            ->isInstanceOf('model');
        
        $this->assert
            ->object($test->testGetModel('controlerTestSecond') )
            ->isInstanceOf('model');
    }
    public function testCallPackage() {
        $test = new \controlerTest();
        $val['param'] = array('_p'=>'controlerTest','_a'=>'testActionAlone');
        $test->setObserverResult($val);


        $test->setView();

        $this->assert
            ->exception(function() use ($test) {
                    $test->testCallPackage('classNotExist');
                })
                ->isInstanceOf('\Exception')
				->hasMessage('The class "classNotExist" does not exist')
                ;
        
        $this->assert
                ->string($test->testCallPackage('controlerTest','testForceAction'))
                ->isNotEmpty()
                ->isEqualTo('force_content_view');

        $this->assert
            ->string($test->testCallPackage('controlerTest'))
                ->isNotEmpty()
                ->isEqualTo(' content_view ');

        $this->assert
            ->string($test->testCallPackage('packageForCallPackageTest'))
                ->isNotEmpty()
                ->isEqualTo(' content_view ');
    }

    
    /**
     * GLOBALS
     */
    public function testAllocatePostGetVariable() {
        $test = new \controlerTest();
        $val['param'] = array('_p'=>'controlerTest','_a'=>'testActionAlone');
        $test->setObserverResult($val);
        $test->setPackageName($val['param']['_p']);

        //Allocate the var
        $this->assert
                ->variable($test->allocatePostGetVariable(array('_GET'=>array('test_get'=>'ok'),'_POST'=>array('test_post'=>'ok')) ) );

        //Allocate the same var : If the var isset, then allocate the var to package_var
        $this->assert
                ->variable($test->allocatePostGetVariable(array('_GET'=>array('test_get'=>'ok'),'_POST'=>array('test_post'=>'ok')) ) );
    }
}
?>