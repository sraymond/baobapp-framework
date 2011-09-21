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

  public function setMeta($type,$content) {
      $this->_meta[$type] = $content;
  }

  /**
   * Generate all the meta and put the result in the class variable
   */
  public function generateMeta() {
    $this->_final_meta = $this->_generate();
  }

  /**
   * Return the meta generated in the html format
   * @return string
   */
  public function GetFinalMeta () {
    $this->generateMeta();
    return $this->_final_meta;
  }

  /**
   * Workflow for generating the html code for the meta tag
   * @return string
   */
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