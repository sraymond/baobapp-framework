<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * @author Stéphane Raymond
 * @version 1.0
 * @package core.lib.meta
 * @copyright Copyright (c) 2009-2099 Stéphane Raymond
 */

class meta {

  protected $_meta;//array
  protected $_final_meta;//string

			
  function __construct(){
      $this->_meta = array('title'=>META_ROBOT_TITLE,'keyword'=>META_ROBOT_KEYWORD,'description'=>META_ROBOT_DESCRIPTION);
  }

  public function GenerateMeta() {
    $this->_final_meta = $this->_generate();
  }

  public function GetFinalMeta () {
    return $this->_final_meta;
  }

  private function _generate() {
    $meta_title = '<title>' . htmlspecialchars($this->_meta['title'], ENT_QUOTES) . '</title> 
		';
		
		$meta_keyword = '<meta name="keywords" content="' . htmlspecialchars($this->_meta['keyword'], ENT_QUOTES) . '" /> 
		' ;
	
		//traitement special du meta description
		if($this->_meta['description'] != '') {
				$dsc = str_replace("\r", "", $this->_meta['description']);
						while (substr($dsc, -1)=="\n") $dsc = substr($dsc, 0, -1);
						while (substr($dsc, 0, 1)=="\n") $dsc = substr($dsc, 1);
						$dsc = str_replace("\n", "<br />", $dsc);
			} else {
				$dsc = '';
			}
		$meta_description = '<meta name="description" content="' . htmlspecialchars($dsc, ENT_QUOTES) . '" /> 
		' ;
		
		$meta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' .$meta_title . $meta_keyword . $meta_description ;
			
		return $meta;	
  }
}  
?>