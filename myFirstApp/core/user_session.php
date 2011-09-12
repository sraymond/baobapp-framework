<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.user.session
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class user_session extends session {

    /**
     * You can manage your session like you whant for more security, in this exemple i have change some few things and after
     * call the parent class to start session
     * La gestion de la session est laissé libre : à vous de modifier son comportement plour plus de sécurité
     */
    protected function startSession() {
        setcookie("Myapp",'',0, '/', WEBMESTRES_SESSION_COOKIE_DOMAIN,false,true);
        $name = '';
        $char = '1234567890azertyuiopmlkjhgfdsqwxcvbnAZERTYUIOPMLKJHGFDSQWXCVBN';
        if(!isset($_COOKIE['Myapp']['name'])) {
            for($i=1; $i <= 10; $i++){
                $n = strlen($char);
                $n = mt_rand(0,$n-1);
                $name .= $char[$n];
            }
            $date = time() + 60;
            setcookie("Myapp[name]",$name);
            setcookie("Myapp[time]",$date);
        }else {
            $name = $_COOKIE['Myapp']['name'];
        }

		session_name($name);

        parent::startSession();
    }
}
?>