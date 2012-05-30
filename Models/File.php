<?php
/**
 * Reborn File Model
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class File extends \CLx\Core\Model {
	public function __construct() {
		parent::__construct();
		// Load Config
		$this->fileConfig = $this->load->config('file');
		// Load Extend Library
		$this->load->extLib('statusCode');
		
		$this->list = array();
		$this->size = 0;
	}

	/**--------------------------------------------------
	 * Parse Path
	 * --------------------------------------------------
	 * @param	string $segments
	 * @return	string $path
	 */
	public function parsePath($segments = NULL) {
		$blacklist = array('[', ']', '&', "'", '"', '?', '/', '\\', '#', ';');
		$path = DIRECTORY_SEPARATOR;
		foreach((array)$segments as $value)
			if($value != '.' || $value != '..') {
				$value = str_replace($blacklist, '', $value);
				$path .= $path == DIRECTORY_SEPARATOR ? $value : DIRECTORY_SEPARATOR . $value;
			}
		return $path == DIRECTORY_SEPARATOR ? '' : $path;
	}

	/**--------------------------------------------------
	 * Recursive Remove Dir
	 * --------------------------------------------------
	 * @param	string $path
	 * @return	boolean
	 */
	public function recursiveRmdir($path = NULL) {
		if(is_dir($path)) {
			$handle = @opendir($path);
			while($file = readdir($handle))
				if($file != '.' && $file != '..')
					$this->recursiveRmdir($path . DIRECTORY_SEPARATOR . $file);
			closedir($handle);
			
			if(rmdir($path))
				return TRUE;
			else
				return FALSE;
		}
		else {
			if(unlink($path))
				return TRUE;
			else
				return FALSE;
		}
	}


	/**--------------------------------------------------
	 * Get File Hash List
	 * --------------------------------------------------
	 * @param	string $path
	 * @return	array $list
	 */
	public function getFileHashList($path = NULL) {
		if(NULL !== $path) {
			if($handle = @opendir($currentPath = FILE_LOCATE . $path)) {
				$filelist = array();
				while($file = readdir($handle))
					array_push($filelist, $file);
				sort($filelist);
				foreach((array)$filelist as $dir)
					if($dir != '.' && $dir != '..') {
						if(@filetype($currentPath. DIRECTORY_SEPARATOR . $dir) != 'dir')
							array_push($this->list, array(
								'hash' => @md5_file($currentPath . DIRECTORY_SEPARATOR . $dir),
								'path' => iconv(mb_detect_encoding($path . '/' . $dir), $this->fileConfig['encode'], $path . '/' . $dir)
							));
						else
							array_push($this->list, array(
								'path' => iconv(mb_detect_encoding($path . '/' . $dir), $this->fileConfig['encode'], $path . '/' . $dir),
							));
						$this->getFileHashList($path . '/' . $dir);
					}
				closedir($handle);
			}
			return $this->list;
		}
		return FALSE;
	}
	
	/**--------------------------------------------------
	 * Get File List
	 * --------------------------------------------------
	 * @param	string $path
	 * @return	array $list
	 */
	public function getFileList($path = NULL) {
		if(NULL !== $path) {
			if($handle = @opendir($currentPath = FILE_LOCATE . $path)) {
				while($dir = readdir($handle))
					if($dir != '.' && $dir != '..') {
						array_push($this->list, array(
							'path' => iconv(mb_detect_encoding($path . '/' . $dir), $this->fileConfig['encode'], $path. '/' . $dir),
							'date' => @filectime($currentPath . DIRECTORY_SEPARATOR . $dir),
							'size' => @filesize($currentPath . DIRECTORY_SEPARATOR . $dir),
							'type' => @filetype($currentPath . DIRECTORY_SEPARATOR . $dir)
						));
						$this->getFileList($path . '/' . $dir);
					}
				closedir($handle);
			}
			sort($this->list);
			return $this->list;
		}
		return FALSE;
	}
	
	/**--------------------------------------------------
	 * Get All File Size
	 * --------------------------------------------------
	 * @param	string $path
	 * @return	array $list
	 */
	public function getAllFileSize($path = NULL) {
		if(NULL !== $path) {
			if($handle = @opendir($currentPath = FILE_LOCATE . $path)) {
				while($dir = readdir($handle))
					if($dir != '.' && $dir != '..') {
						if(is_file($currentPath . DIRECTORY_SEPARATOR . $dir))
							$this->size += @filesize($currentPath . DIRECTORY_SEPARATOR . $dir);
						$this->getAllFileSize($path . '/' . $dir);
					}
				closedir($handle);
			}
			return $this->size;
		}
		return FALSE;
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
