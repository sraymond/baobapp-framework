<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * DESCRIPTION
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package exception.view
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class exceptions_view extends user_view  {

    /**
     * content de la 404
     *
     */
    public function home() {
        $html = '<h1>404 : page not found</h1>
                 <p class="first">
                    just implement what you whant for the 404 page not found :)
                 </p>
                ';
        $this->output($html);
    }

    /**
     * Affichage de la 500
     */
    public function error500(){
        $html = '<h1>500</h1>
                 <p class="first">
                    big pb :(.
                 </p>
                ';
        $this->output($html);
    }
}
?>