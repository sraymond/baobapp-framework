<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * DESCRIPTION
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package main.view
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class main_view extends user_view  {

    /**
     * content de la home page de baobapp
     *
     */
    public function home() {
        $html = '<h1>This is a test for using the Baobapp Framework</h1>
                 <p class="first">
                    You have a menu at the header of this web page. You can have access to different part of the test.
                    It\'s show you different way to implement a user call.
                 </p>

                 <ul>
                    You have 3 package :
                    <li>"Main" : this package is mandatory. it\'s the package called when you call this url : http://mydomain.com</li>
                    <li>"test" : this package is for the test. in the test.php (controler for the test package) with have implemented 3 methodes</li>
                    <li>"exceptions" : just write what you whant in the URL :=> call the exception package and disply the 404.</li>
                 </ul>
            ' ;

		$this->output($html);		
    }
}
?>