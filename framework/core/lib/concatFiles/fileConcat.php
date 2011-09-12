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

class fileConcat {

	private $output_file_path = null;
	private $input_files_path = null;
	private $input_files_extensions = null;

	public function __construct($output_file_path, $input_files_path = null, $input_files_extensions = null){
		$this->input_files_path = $input_files_path ? $input_files_path : array();
		$this->output_file_path = $output_file_path;
		$this->input_files_extensions = $input_files_extensions;
	}

	/**
	 * Permet d'ajouter un dir à parcourir pour des fichiers de type $this->input_files_extensions à compresser
	 *
	 * @param string $dir_path
	 * @param bolean $find_in_subdirs : true permet de faire de la récursivité
	 */
	public function addDir($dir_path, $find_in_subdirs = false) {
		//recupération des fichiers contenu dans le dir
	    $contents = getDirFiles($dir_path);
	    //tri dans l'ordre le tableau pour ordonnées les fichiers
		asort($contents);

		foreach ($contents as $content) {
			if ($content != '.svn') {
				$content = $dir_path . '/' . $content;
				if($find_in_subdirs && is_dir($content)) {
					$this->addDir($content);
				} elseif(is_file($content)) {
					$this->addFile($content);
				}
			}
		}
	}
	
	/**
	 * rajoute un fichier dans la liste des fichiers qui devront passer à la moulinette pour être optimisé
	 *
	 * @param string $file_path
	 */
	public function addFile($file_path) {
		//si on a setté $this->input_files_extensions : on ne prend que les fichier du type spécifié
	    if ($this->input_files_extensions) {
			$pathinfo = pathinfo($file_path);
			if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $this->input_files_extensions)) {
				return;
			}
		}
        //tout est ok : le fichier est ajouté à la lliste des fichiers qui seront à traiterÒ
		$this->input_files_path[] = $file_path;
	}
	
	/**
	 * Rassemble le contenu de chaque fichier et va etre optimisé
	 *
	 */
	public function compute() {
		//si le fichier existe : on le sup car on va en recréer un autre
	    if(file_exists($this->output_file_path)) {
		  unlink($this->output_file_path);    
		}
		
		// Compilation
		$output = array();
		foreach ($this->input_files_path as $input_path) {
			$output[] = file_get_contents($input_path);
		}

		//on rassemble tout le texte dans une seule string et on le sépare par des retours à la line
		$output = implode("\r\n\r\n", $output);

		//Optimisation + écriture du fichier final
		$output = $this->optimize($output);
        file_put_contents($this->output_file_path, $output);
	}
	
	/**
	 * retourne la liste des fichiers qui seront rassemblés
	 *
	 * @return array
	 */
	public function ListFileInclude() {	    
	   return $this->input_files_path;    
	}
	
	/**
	 * Workflow d'optimisation : moteur à définir pour chaque enfant
	 *
	 * @param string $output
	 * @return string
	 */
	protected function optimize($output) {
		return $output;
	}
}
?>