<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.user.view
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class viewTest extends view {

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
}
?>