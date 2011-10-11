<?php
/**
 * Test class for the core.controler package
 * @tutorial : To test protected methods from the class controler, we need to make
 * ridges and re declare new public functions or variables
 */
class packageForCallPackageTest_view extends view{

    public function  __construct() {}

    public function getHeader() {
        return $this->header();
    }
    protected function header(){
        return 'header';
    }

    public function getFooter(){
        return $this->footer();
    }
    protected function footer(){
        return 'footer';
    }

    public function getGenerateCssContent(){
        return $this->generateCssContent();
    }
    protected function generateCssContent(){
        return 'css';
    }

    public function getGenerateJsContent(){
        return $this->generateJsContent();
    }
    protected function generateJsContent(){
        return 'js';
    }
    
    public function home(){
        $this->output(' content_view ');
    }
}
?>