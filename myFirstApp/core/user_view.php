<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.user.view
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class user_view extends view {

    public function  __construct() {
        parent::__construct();
    }

    /**
     * I do nothing else. But if you whant, you can change the way how to output the scring from the package view
     * @param boolean $call_package 
     */
    public function  toString($call_package = false) {
        return parent::toString($call_package);
    }

    /**
     * Global header
     *
     * @return string
     */
    protected function header() {
        $html = 
                $this->headerHtmlOpenContent() .
                
                $this->meta() .
                
                $this->generateCssContent() .
                
                "\t" . '</head>' . "\n" .
                
                "\t" . '<body>' . "\n" .
                    
                $this->headerCoreContent() ;
   
        return $html; 
    }
    
	/**
     * html header
     *
     * @return $string html
     */
    protected function headerHtmlOpenContent() {
        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n".'
                <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">'."\n".'
               '. "\t" . '<head>' . "\n";
    }       
       
    /**
     * Header of your layout
     *
     * @return string $html
     */
    protected function headerCoreContent() {
        return '<div id=header>
                    <ul>
                        <li id="first_menu_id" class="link_menu"><a href="/index.php">Home</a></li>
                        <li><a href="/test/test1/" class="link_menu">Test action 1</a></li>
                        <li><a href="/test/test2/" class="link_menu">Test action 2</a></li>
                        <li><a href="/index.php?_p=test&_a=test3" class="link_menu">Test action 3</a></li>
                    </ul>
               </div>
               <div id="general_content">
                    <a href="/" class="logo over_logo" title="Site personnel de Stéphane Raymond Ingénieur consultant sénior PHP MySQL, architecte LAMP"></a>
                    <div class="content">
                ';
    }
    
    /**
     * This is the footer of the layout
     *
     * @return unknown
     */
	protected function footer() {
       $html = 
            //on ferme le site

            '       </div>
             </div>
             <div id="footer">
             <a id="twitter" class="iconeset" href="http://twitter.com/#!/sraymond38/" title="Suivez moi sur twitter" target="_blanc"></a>
             <a id="viadeo" class="iconeset" href="http://www.viadeo.com/fr/profile/stephane.raymond2" title="Partageons notre réseau sur Viadéo" target="_blanc"></a>
             </div>' .

        $this->generateJsContent() ;
        //on ferme les balises du site
        $html .=   "\t" . '<body>' . "\n".
            	   "\t" . '</html>';
        return $html;        	         
    }

	/**
     * Manage the js file
     *
     * @return string js content
     */
    protected function generateJsContent() {
        
        //generation du fichier de cache css
        if(getUrlParameter('_js') == 'build' && getConfig('plateform','mode') == 'dev') {
            $this->createCompactZipJs();
            exit();
        }

        //cache js active : on utilise le fichier generer en dev et commite en prod ou preprod
        if(getConfig('js','cache') == true && file_exists(ROOT_WWW_PATH . $this->relative_path_js . getConfig('jsfilename','outputName')) === true) {
            return '<script type="text/javascript" src="'. $this->relative_path_js . getConfig('jsfilename','outputName') . '"></script>' . "\r\n";
        }

        ## le fichier js minifie n'existe pas ou on est en mode dev : on va loader les fichiers js qui sont dans le rep
        $html = '';
        $list = addDir(ROOT_WWW_PATH . $this->relative_path_js . $this->dir_user_js_file_name ,'js');
        
        //construction html
        if(count($list) > 0) {
            foreach ($list as $clef => $value) {
                $relatif_path = explode(ROOT_WWW_PATH, $value);
                $html .= '<script type="text/javascript" src="'. $relatif_path[1] . '"></script>' . "\r\n";
            }
        }
        return $html;
    }
    
    /**
     * Manage the css files
     *
     * @return string css content
     */
    protected function generateCssContent() {
        
        //generation du fichier de cache css
        if(getUrlParameter('_css') == 'build' && getConfig('plateform','mode') == 'dev') {
            $this->createCompactZipCss();
            exit();
        }

        //cache css active : on utilise le fichier generer en dev et commite en prod ou preprod
        if(getConfig('css','cache') == true && file_exists(ROOT_WWW_PATH . $this->relative_path_css . getConfig('cssfilename','outputName')) === true) {
            $html = '<link rel="stylesheet" type="text/css" href="' . $this->relative_path_css . getConfig('cssfilename','outputName') . '" />' . "\r\n" ;
            if(getConfig('css','hack') == 'on'  || getConfig('css','hack') == 1) {
            	if(getConfig('css_hack_url',getOs()) != null){
            		$html .= '<link rel="stylesheet" type="text/css" href="' . $this->relative_path_css . getConfig('css_hack_url',getOs()) . '" />' . "\r\n";
            	}
            }
            return $html;
        }

        ## le fichier css minifie n'existe pas ou on est en mode dev : on va loader les fichiers css qui sont dans le rep
        $html = '';
        $list = addDir(ROOT_WWW_PATH . $this->relative_path_css . $this->dir_user_css_file_name ,'css');
        
        //construction html
        if(count($list) > 0) {
            foreach ($list as $clef => $value) {
                $relatif_path = explode(ROOT_WWW_PATH, $value);
                $html .= '<link rel="stylesheet" type="text/css" href="'. $relatif_path[1] . '" />' . "\r\n";
            }
            //permet de rajouter un hack dédié a l'os (dif font win & mac)
            if(getConfig('css','hack') == 1 || getConfig('css','hack') == 'on') {
            	if(getConfig('css_hack_url',getOs()) != null){
            		$html .= '<link rel="stylesheet" type="text/css" href="' . $this->relative_path_css . getConfig('css_hack_url',getOs()) . '" />' . "\r\n";
            	}
            }
        }
        return $html;
    }
}
?>