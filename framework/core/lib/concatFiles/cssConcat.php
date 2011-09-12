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

class cssConcat extends fileConcat {

	public function __construct($output_file_path, $input_files_path = null) {
		parent::__construct($output_file_path, $input_files_path, array('css'));
	}

	/**
	 * Surcharge : Optimisation de output par parsing csstidy
	 *
	 * @param string $output
	 * @return string optimized
	 */
	protected function optimize($output) {
		//on doit passer en en_US
	    $old_local = $GLOBALS['util_localisation']->getLang();
		$GLOBALS['util_localisation']->setLang('en_US');

		$cssTidy = new csstidy();

		$cssTidy->set_cfg('remove_last_;',TRUE);
		$cssTidy->set_cfg('remove_bslash;',TRUE);
		$cssTidy->set_cfg('compress_colors',true);
		$cssTidy->set_cfg('compress_font-weight',true);
		$cssTidy->set_cfg('lowercase_s',false);
		$cssTidy->set_cfg('optimise_shorthands',1);
		$cssTidy->set_cfg('case_properties',1);
		$cssTidy->set_cfg('sort_properties',false);
		$cssTidy->set_cfg('sort_selectors',false);
		$cssTidy->set_cfg('merge_selectors',0);
		$cssTidy->set_cfg('css_level','CSS2.1');
		$cssTidy->set_cfg('discard_invalid_properties',false);
	    $cssTidy->set_cfg('preserve_css',true);
	    $cssTidy->set_cfg('timestamp',false);

	    $cssTidy->load_template('highest_compression');

		$cssTidy->parse($output);

		$output = $cssTidy->print->plain();

		//on remet en place la langue de l'utilisateur
		$GLOBALS['util_localisation']->setLang($old_local);

		return $output;
	}
}
?>