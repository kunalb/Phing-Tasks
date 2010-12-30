<?php

require_once "phing/Task.php";

/**
 * YUICompressorTask
 * 
 * Compresses CSS and Javascript files before deployment.
 *
 * Example Usage:
 * <YUICompressor path="path/to/yuicompressor" mapping=".dev">
 * 	<fileset dir = ".">
 *	</fileset>
 * </YUICompressor>
 *
 * The mapping attribute is used in renaming the files as they are compressed.
 * In the given usage, '.dev' will be removed from all the file names on output.
 *
 */
class YUIMinTask extends Task {
	/*
	 * Variables for this.
	 */
	protected $filesets = Array();
	protected $file;
	protected $showWarnings = true;
	protected $haltOnFailure = false;
	protected $path;
	protected $mapper;

	/**
	 * The setter for the attribute "message"
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Sets the Warnings flag in the call to YUICompressor.
	 */
	public function setShowWarnings($show) {
		$this->showWarnings = StringHelper::booleanValue($show);
	}

	/**
	 * Setting a single file from within the attributes.
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * Create the fileset for this function.
	 */
	public function createFileSet() {
		$num = array_push($this->filesets, new FileSet());
		return $this->filesets[$num-1];
	}

	public function createMapper() {
		if( !empty( $this->mapper ) )
			throw new BuildException( "Can only support a single mapper." );

		$this->mapper = new Mapper($this->project);
		return $this->mapper;
	}


	/**
	 * The init method: Do init steps.
	 */
	public function init() {
	}

	/**
	 * The main entry point method.
	 */
	public function main() {
		if(!isset($this->file) && count($this->filesets) == 0)
			throw new BuildException("Missing either a nested fileset or attribute 'file' set");

		if($this->file instanceof PhingFile)
			$this->lint($this->file->getPath());
		else {
			$project = $this->getProject();
			foreach($this->filesets as $fs) {
				$ds = $fs->getDirectoryScanner($project);
				$files = $ds->getIncludedFiles();
				$dir = $fs->getDir($this->project)->getPath();
				foreach($files as $file) {
					$this->compress($dir.DIRECTORY_SEPARATOR.$file);
					$this->log( "Compressed file $file." );
				}
			}
		}
	}

	/**
	 * The actual compression.
	 */
	protected function compress( $file ) {
		$mapperImplementation = $this->mapper->getImplementation();
		$minifiedArray = $mapperImplementation->main( $file );
		$minified = "-o {$minifiedArray[0]}";
		$verbose = ($this->showWarnings)? "-v" : "";
		$command = "java -jar {$this->path} $file $minified $verbose";

		exec($command, $output, $returnvar);
	}
}
