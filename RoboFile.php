<?php

include '.tk/RoboFileBase.php';

class RoboFile extends RoboFileBase {
	public function directoriesStructure() {
		return array( 'includes' );
	}

	public function fileStructure() {
		return array( 'buddyforms-hook-fields.php', 'composer.json', 'license.txt', 'readme.txt' );
	}

	public function cleanPhpDirectories() {
		return array( 'includes/resources/tgm' );
	}

	public function pluginMainFile() {
		return 'buddyforms-hook-fields';
	}

	public function pluginFreemiusId() {
		return 412;
	}

	public function minifyAssetsDirectories() {
		return array(  );
	}

	public function minifyImagesDirectories() {
		return array();
	}

	/**
	 * @return array Pair list of sass source directory and css target directory
	 */
	public function sassSourceTarget() {
		return array( array( 'scss/source' => 'assets/css' ) );
	}

	/**
	 * @return string Relative paths from the root folder of the plugin
	 */
	public function sassLibraryDirectory() {
		return 'scss/library';
	}
}