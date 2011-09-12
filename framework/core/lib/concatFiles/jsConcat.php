<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Fonctions de gestion des URL et de la navigation.
 *
 * @author Stéphane
 * @version 1.0
 * @package core.lib.concatFiles
 * @copyright Copyright (c) 2009-2099 BAOBAPP
 */

require_once(ROOT_FRAMEWORK_PATH . '/core/lib/concatFiles/fileConcat.php');

class jsConcat extends fileConcat {

	public function __construct($output_file_path, $input_files_path = null) {
		parent::__construct($output_file_path, $input_files_path, array('js'));
	}

	protected function optimize($output) {
		return JSMin::minify($output);
	}
}
?>