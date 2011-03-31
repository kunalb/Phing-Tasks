<?php

require_once "phing/Task.php";

/**
 * WPPoT Task
 * 
 * Used to generate the .pot file for a plugin.
 *
 */
class WPPotTask extends Task {
	/*
	 * Variables for this.
	 */
	protected $path;
	protected $output;
	protected $type;

	/**
	 * The setter for the attribute "message"
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	public function setOutput($output) {
		$this->output = $output;
	}

	public function setType($type) {
		$this->type = $type;
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
		if( !isset($this->path) )
			throw new BuildException("Missing the path to the .pot builder.");

		if( !isset($this->output) )
			throw new BuildException("Missing the name of the output file.");

		$command = "php {$this->path}/makepot.php {$this->type} . {$this->output}";
		exec($command, $output, $result);

		if( $result == 0 ) 
			$this->log("{$this->output} generated.");
		else
			throw new BuildException("Unable to generate the .pot file.");
	}
}
