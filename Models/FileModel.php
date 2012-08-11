<?php
/**
 * RNFileSystem File Model
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

class FileModel extends \CLx\Core\Model {
	
	public function __construct() {
		parent::__construct();
		
		// Load Config
		$this->file_config = \CLx\Core\Loader::config('Config', 'file');
		
		// Load Library
		\CLx\Core\Loader::library('StatusCode');
		
		$this->list = array();
	}

	/**--------------------------------------------------
	 * Parse Path
	 * --------------------------------------------------
	 * @param	string $segments
	 * @return	string $path
	 */
	public function parsePath($segments = NULL) {
		$blacklist = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
		$path = '/';
		foreach((array)$segments as $value)
			if($value != '.' || $value != '..') {
				$value = str_replace($blacklist, '', $value);
				$path .= $path == '/' ? $value : '/' . $value;
			}
		return $path;
	}
	
	/**--------------------------------------------------
	 * Get File Mimetype
	 * --------------------------------------------------
	 */
	public function getMimetype($filepath) {
	    if(function_exists("mime_content_type"))
	       $mimetype = mime_content_type($filepath);
	    else {
	       $fileinfo = finfo_open(FILEINFO_MIME);
	       $mime = finfo_file($fileinfo, $filepath);
	       finfo_close($fileinfo);
	    }
		
	    $mimetype = explode(';', $mimetype);
	    return trim($mimetype[0]);
	}
}
