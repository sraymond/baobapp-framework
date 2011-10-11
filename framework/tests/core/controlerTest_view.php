<?php
/**
 * Test class for the core.controler package
 * @tutorial : To test protected methods from the class controler, we need to make
 * ridges and re declare new public functions or variables
 */
class controlerTest_view extends viewTest{
    
    public function testViewActionCall($mode = ''){
        switch ($mode) {
            case 'output':
                $this->output(' content_view ');
                break;
            default :
                return 'ok';
                break;
        }
    }

    public function testViewForceActionCall(){
        $this->output('force_content_view');
    }
}
?>