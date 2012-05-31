<?php
/**
 * Sim File System
 * 
 * @package		SimFS
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/SimFS
 */

class SimFS {
	
	/**
	 * @var string
	 */
	private static $_root;
	
	/**
	 * @var array
	 */
	private static $_record = array();
	
	/**
	 * @var array
	 */
	private static $_config = array();
	
	private function __construct() {}
	
	/**
	 * Initialize
	 * 
	 * @param string
	 */
	public static function Init($root, $config = NULL) {
		self::$_root = $root;
		self::$_config = $config;
		
		// Load files record list
		if(file_exists(self::$_root . '/record.json')) {
			$handle = fopen(self::$_root . '/record.json', 'r');
			$json = NULL;
			while($data = fread($handle, 1024))
				$json .= $data;
			self::$_record = json_decode($json, TRUE);
			fclose($handle);
		}
		
		// Make root folder
		if(!file_exists(self::$_root))
			mkdir(self::$_root, 0755, TRUE);
	}
	
	/**
	 * Record Write-back
	 */
	private static function Save() {
		$handle = fopen(self::$_root . '/record.json', 'w+');
		fwrite($handle, json_encode(self::$_record));
		fclose($handle);
	} 
	
	/**
	 * Index Files
	 */
	public static function Index() {
		return json_encode(self::$_record);
	}
	
	/**
	 * Revert File
	 * 
	 * @param string
	 */
	public static function Revert($path, $version) {
		// Check file is exists
		if(!isset(self::$_record[$path]))
			return FALSE;
		
		// Check File version is exists
		if(self::$_record[$path]['hash'][$version])
			return FALSE;
		
		// Delete new version
		for($i = 0;$i < $version;++$i) {
			$hash = array_shift(self::$_record[$path]['hash']);
			
			// if unlink error return false and save record
			if(!unlink(self::$_root . '/' . $hash)) {
				self::Save();
				return FALSE;
			}
		}
		
		// Record Write-back
		self::Save();
		return TRUE;
	}
	
	/**
	 * Move File
	 * 
	 * @param string
	 * @param string
	 */
	public static function Move($sim_src, $sim_dest) {
		// Check Sim Source and Sim Destination
		if(!isset(self::$_record[$sim_src]) || isset(self::$_record[$sim_dest]))
			return FALSE;
		
		self::$_record[$sim_src]['path'] = $sim_dest;
		self::$_record[$sim_dest] = self::$_record[$sim_src];
		
		unset(self::$_record[$sim_src]);
		
		// Record Write-back
		self::Save();
		return TRUE;
	}
	
	/**
	 * Create File
	 * 
	 * @param string
	 * @param string
	 */
	public static function Create($real_src, $sim_dest) {
		// Check Real Source and Sim Destination
		if(!file_exists($real_src) || isset(self::$_record[$sim_dest]))
			return FALSE;
		
		// Generate Unique-Hash for File
		do {
			$hash = hash('md5', hash_file('md5', $real_src) . rand());
		}
		while(file_exists(self::$_root . '/' . $hash));
		
		// Copy Real File to SimFS
		if(!copy($real_src, self::$_root . '/' . $hash))
			return FALSE;
		
		// Delete real file
		// unlink($real_src);
		
		// Add new record
		self::$_record[$sim_dest] = array(
			'path' => $sim_dest,
			'hash' => array($hash)
		);
		
		// Record Write-back
		self::Save();
		return TRUE;
	}
	
	/**
	 * Update File
	 * 
	 * @param string
	 * @param string
	 */
	public static function Update($real_src, $sim_dest) {
		// Check Real Source and Sim Destination
		if(!file_exists($real_src) || !isset(self::$_record[$sim_dest]))
			return FALSE;

		// Generate Unique-Hash for File
		do {
			$hash = hash('md5', hash_file('md5', $real_src) . rand());
		}
		while(file_exists(self::$_root . '/' . $hash));
		
		// Copy Real File to SimFS
		if(!copy($real_src, self::$_root . '/' . $hash))
			return FALSE;
		
		// Delete real file
		// unlink($real_src);
		
		// Add new record
		array_unshift(self::$_record[$sim_dest]['hash'], $hash);
		
		// Record Write-back
		self::Save();
		return TRUE;
	}
	
	/**
	 * Read File
	 * 
	 * @param string
	 * @param boolean
	 */
	public static function Read($path, $seek = NULL, $version = 0) {
		// Check path exists
		if(!isset(self::$_record[$path]))
			return FALSE;
		
		// Check file version exists
		if(!isset(self::$_record[$path]['hash'][$version]))
			return FALSE;
		
		// Normal download
		if(NULL === $seek) {
			// ob_end_flush();
			readfile(self::$_root . '/' . self::$_record[$path]['hash'][$version]);
			return TRUE;
		}
		
		// Resume download
		if(is_int($seek) && $seek <= filesize(self::$_root . '/' . self::$_record[$path]['hash'][$version])) {
			// ob_end_flush();
			$handle = fopen(self::$_root . '/' . self::$_record[$path]['hash'][$version], 'rb');
			fseek($handle, $seek);
			fpassthru($handle);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Delete File
	 * 
	 * @param string
	 */
	public static function Delete($path) {
		// Check Path is exists
		if(!isset(self::$_record[$path]))
			return FALSE;
		
		// Delete All File version
		foreach(self::$_record[$path]['hash'] as $version)
			if(!unlink(self::$_root . '/' . $version))
				return FALSE;
		
		// Delete record
		unset(self::$_record[$path]);
		
		// Record Write-back
		self::Save();
		return TRUE;
	}
}
