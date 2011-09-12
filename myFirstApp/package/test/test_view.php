<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * DESCRIPTION
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package test.view
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class test_view extends user_view  {


    public function home () {
        $html = '<h1>Home page of the test package</h1>
                 <p class="first">
                    this is the first test ;-).
                 </p>
                 <ul>
                 You can access to this result by those URL
                 <li>http://www.myapp.com/test</li>
                 <li>http://www.myapp.com/test/test1</li>
                 <li>http://www.myapp.com/index.php?_p=test&_a=test1/</li>
                 </ul>
                 ';
        
        $this->output($html);
    }

    public function test2 () {
        $html = '<h1>test2 of the test package</h1>
                 <p class="first">
                    this is the second test ;-).
                 </p>
                 
                 ';

        $this->output($html);
    }

    public function test3 () {
        $html = '<h1>test3 of the test package</h1>
                 <p class="first">
                    this is the third test ;-).
                 </p>

                 ';

        $this->output($html);
    }
}
?>