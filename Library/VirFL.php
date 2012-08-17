<?php
/**
 * RNFileSystem Virtual File Layer
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

class VirFL {
	
	/**
	 * @var string
	 */
	private static $_root;
	
	/**
	 * @var array
	 */
	private static $_record;
	
	/**
	 * @var int
	 */
	private static $_revert = 0;
	
	/**
	 * @var boolean / int
	 */
	private static $_is_error = false;
	
	private function __construct() {}
	
	/**
	 * Initialize
	 * 
	 * @param string
	 */
	public static function init($root, $revert = NULL) {
		self::$_root = $root;
		if(NULL !== $revert)
			self::$_revert = $revert;
		
		// Make root folder
		if(!file_exists(self::$_root . '/data'))
			mkdir(self::$_root . '/data', 0755, TRUE);
		
		// Load files record list
		if(!file_exists(self::$_root . '/record.db3')) {
			self::$_record = new PDO('sqlite:' . self::$_root . '/record.db3');
			self::$_record->query(
				'CREATE TABLE files (' .
					'path TEXT NOT NULL,' .
					'type TEXT NOT NULL,' .
					'size INTEGER,' .
					'hash TEXT,' .
					'version INTEGER,' .
					'revision TEXT,' .
					'PRIMARY KEY(path ASC)' .
				')'
			);
			
			# Insert Default Data
			self::$_record->query('INSERT INTO files (path, type) VALUES ("/", "dir")');
		}
		else
			self::$_record = new PDO('sqlite:' . self::$_root . '/record.db3');
		
		// Extend SQLite RegExp Function
		self::$_record->sqliteCreateFunction('REGEXP', function($pattern, $subject) {
			return preg_match("/{$pattern}/", $subject);
		}, 2);
	}
	 
	
	/**
	 * Index Files
	 */
	public static function index($path = '/') {
		if('/' === $path) {
			$list = array();
			$sth = self::$_record->query('SELECT * FROM files');
			while($row = $sth->fetch()) {
				if('file' == $row['type']) {
					$list[$row['path']] = array(
						'type' => 'file',
						'size' => $row['size'],
						'hash' => $row['hash'],
						'version' => $row['version']
					);
				}
				else
					$list[$row['path']] = array('type' => 'dir');
			}
		}
		else {
			if(!self::isExists($path))
				return NULL;
			
			$list = NULL;
			
			$sth = self::$_record->prepare('SELECT * FROM files WHERE path=:path');
			$sth->execute(array(':path' => $path));
			$result = $sth->fetch();
			
			if('file' == $result['type'])
				return $list[$result['path']] = array(
					'type' => 'file',
					'size' => $result['size'],
					'hash' => $result['hash'],
					'version' => $result['version']
				);
			else
				$list[$result['path']] = array('type' => 'dir');
			
			$regex_path = sprintf('^\/%s\/', str_replace('/', '\/', trim($path, '/')));
			$sql = sprintf('SELECT * FROM files WHERE path REGEXP "%s"', $regex_path);
			$sth = self::$_record->prepare($sql);
			$sth->execute();
			
			while($result = $sth->fetch()) {
				if('file' == $result['type'])
					$list[$result['path']] = array(
						'type' => 'file',
						'size' => $result['size'],
						'hash' => $result['hash'],
						'version' => $result['version']
					);
				else
					$list[$result['path']] = array('type' => 'dir');
			}
		}

		return $list;
	}
	
	/**
	 * Revert File
	 * 
	 * @param string
	 */
	// FIXME need test
	public static function revert($path, $version = NULL) {
		// Check file is exists
		if(!self::isExists($path))
			return FALSE;
		
		if(NULL == $version)
			return FALSE;
		
		// Check File version is exists
		$sth = self::$_record->prepare('SELECT * FROM files WHERE path=:path');
		$sth->execute(array(':path' => $path));
		$result = $sth->fetch();
		
		// Check file version exists
		if($result['version'] < $version && $version < 0)
			return FALSE;
		
		$result['revision'] = json_decode($result['revision'], TRUE);
		
		// Delete new version
		for($i = count($result['revision'])-1;$i > $version;--$i) {
			$hash = array_pop($result['revision']);
			unlink(self::$_root . '/data/' . $hash);
		}
		
		$sth = self::$_record->prepare('UPDATE files SET version=:version, revision=:revision WHERE path=:path');
		$sth->execute(array(
			':path' => $path,
			':version' => $version,
			':revision' => json_encode($result['revision'])
		));

		return TRUE;
	}
	
	/**
	 * Move File
	 * 
	 * @param string
	 * @param string
	 */
	// FIXME need test
	public static function move($sim_src, $sim_dest) {
		// Check Sim Source and Sim Destination
		if(!self::isExists($sim_src) || self::isExists($sim_dest))
			return FALSE;
		
		// Create full directory path
		self::createFullDirPath($sim_dest, self::type($sim_src));

		// Change old path to new path
		$sth = self::$_record->prepare('UPDATE files SET path=:new_path WHERE path=:path');
		$sth->execute(array(
			':path' => $sim_src,
			':new_path' => $sim_dest
		));
		
		//FIXME Bug
		if('dir' == self::type($sim_dest)) {
			// Load file path
			$regex_path_sql = sprintf('^\/%s\/', str_replace('/', '\/', trim($sim_src, '/')));
			$sql = sprintf('SELECT path FROM files WHERE path REGEXP "%s"', $regex_path_sql);
			$sth = self::$_record->prepare($sql);
			$sth->execute();
			
			// Change sub directory to new path
			$regex_path = sprintf('/^\/%s\/(.*)/', str_replace('/', '\/', trim($sim_src, '/')));
			while($row = $sth->fetch()) {
				if(preg_match($regex_path, $row['path'], $match)) {
					$sth = self::$_record->prepare('UPDATE files SET path=:new_path WHERE path=:path');
					$sth->execute(array(
						':path' => $row['path'],
						':new_path' => $sim_dest . '/' . $match[1]
					));
				}
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Create File or Create Directory
	 * 
	 * @param string
	 * @param string
	 */
	public static function create($sim_path, $real_path = NULL) {
		if(NULL !== $real_path) {
			// Check Real Source and Sim Destination
			if(!file_exists($real_path) || self::isExists($sim_path))
				return FALSE;
			
			// Create full directory path
			self::createFullDirPath($sim_path, 'file');
			
			// Generate Unique-Hash for File
			do {
				$hash = hash('md5', rand());
			}
			while(file_exists(self::$_root . '/data/' . $hash));
			
			// Copy Real File to VirFL
			if(!copy($real_path, self::$_root . '/data/' . $hash))
				return FALSE;
			
			// Add new record
			$sth = self::$_record->prepare('INSERT INTO files (path, type, size, hash, version, revision) VALUES (:path, :type, :size, :hash, :version, :revision)');
			$sth->execute(array(
				':path' => $sim_path,
				':type' => 'file',
				':size' => filesize(self::$_root . '/data/' . $hash),
				':hash' => hash_file('md5', self::$_root . '/data/' . $hash),
				':version' => 0,
				':revision' => json_encode(array($hash))
			));
		}
		else {
			// Check Sim Path
			if(self::isExists($sim_path))
				return FALSE;
	
			// Create full directory path
			self::createFullDirPath($sim_path, 'dir');
		}

		return TRUE;
	}
	
	/**
	 * Create Full Directory File
	 * 
	 * @param string
	 * @param string
	 */
	private static function createFullDirPath($path, $type) {
		$segments = explode('/', trim($path, '/'));

		// If type is file then pop filename
		if('file' == $type)
			array_pop($segments);
		
		// Create
		$full_path = '';
		foreach($segments as $segment) {
			$full_path .= '/' . $segment;
			if(!self::isExists($path)) {
				// Add new record
				$sth = self::$_record->prepare('INSERT INTO files (path, type) VALUES (:path, :type)');
				$sth->execute(array(
					':path' => $full_path,
					':type' => 'dir'
				));
			}
			elseif('file' != self::type($path)) {
				// Add new record
				$sth = self::$_record->prepare('INSERT INTO files (path, type) VALUES (:path, :type)');
				$sth->execute(array(
					':path' => $full_path,
					':type' => 'dir'
				));
			}
			else
				return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Update File
	 * 
	 * @param string
	 * @param string
	 */
	// FIXME need test
	public static function update($sim_path, $real_path) {
		// Check Real Source and Sim Destination
		if(!file_exists($real_path) || !self::isExists($sim_path))
			return FALSE;

		// Generate Unique-Hash for File
		do {
			$hash = hash('md5', rand());
		}
		while(file_exists(self::$_root . '/data/' . $hash));
		
		// Copy Real File to VirFL
		if(!copy($real_path, self::$_root . '/data/' . $hash))
			return FALSE;
		
		self::$_record->prepare('SELECT hash,version,revision FROM files WHERE path=:path');
		$sth = self::execute(array(
			':path' => $sim_path
		));
		$result = $sth->fetch();
		
		// Add new record
		$result['revision'] = json_decode($result['revision'], TRUE);
		array_unshift($result['revision'], $hash);
		
		self::$_record->prepare('UPDATE files SET hash=:hash, version=:versoin, revision=:revision WHERE path=:path');
		$sth = self::execute(array(
			':path' => $sim_path,
			':hash' => hash_file('md5', self::$_root . '/data/' . $hash),
			':version' => $result['version']+1,
			':revision' => json_encode($result['revision'])
		));
		
		return TRUE;
	}
	
	/**
	 * Read File
	 * 
	 * @param string
	 * @param boolean
	 */
	public static function read($path, $seek = NULL, $version = NULL) {
		// Check path exists
		if(!self::isExists($path))
			return FALSE;
		
		$sth = self::$_record->prepare('SELECT * FROM files WHERE path=:path');
		$sth->execute(array(':path' => $path));
		$result = $sth->fetch();
		
		if(NULL == $version)
			$version = count($result['version'])-1;
		
		// Check file version exists
		if($result['version'] < $version && $version < 0)
			return FALSE;
		
		$result['revision'] = json_decode($result['revision'], TRUE);
		
		// Normal download
		if(NULL === $seek) {
			// ob_end_flush();
			header('Content-Type: ' . mime_content_type(self::$_root . '/data/' . $result['revision'][$version]));
			readfile(self::$_root . '/data/' . $result['revision'][$version]);
			return TRUE;
		}
		
		// Resume download
		if(is_int($seek) && $seek <= filesize(self::$_root . '/data/' . $result['hash'][$version])) {
			// ob_end_flush();
			header('Content-Type: ' . mime_content_type(self::$_root . '/data/' . $result['revision'][$version]));
			$handle = fopen(self::$_root . '/data/' . $result['revision'][$version], 'rb');
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
	public static function delete($path) {
		// Check Path is exists
		if(!self::isExists($path))
			return FALSE;
		
		if('file' == self::type($path)) {
			// Load file information
			$sth = self::$_record->prepare('SELECT revision FROM files WHERE path=:path');
			$sth->execute(array(':path' => $path));
			$result = $sth->fetch();
			
			// Delete all file version
			$result['revision'] = json_decode($result['revision'], TRUE);
			foreach((array)$result['revision'] as $hash)
				unlink(self::$_root . '/data/' . $hash);
			
			// Delete file record
			$sth = self::$_record->prepare('DELETE FROM files WHERE path=:path');
			$sth->execute(array(':path' => $path));
		}
		else {
			if('/' !== $path) {
				// Delete directory record
				$sth = self::$_record->prepare('DELETE FROM files WHERE path=:path');
				$sth->execute(array(':path' => $path));
				
				$regex_path = sprintf('^\/%s\/', str_replace('/', '\/', trim($path, '/')));
			}
			else
				$regex_path = '^\/.+';
			
			// Load all files record
			$sth = self::$_record->prepare('SELECT hash FROM files WHERE path=:path, type="file"');
			$sth->execute(array(':path' => $path));
			
			// Delete all file version
			while($row = $sth->fetch()) {
				$result['revision'] = json_decode($result['revision'], TRUE);
				foreach($result['revision'] as $hash)
					unlink(self::$_root . '/data/' . $hash);
			}
			
			// Delete all file record
			$sql = sprintf('DELETE FROM files WHERE path REGEXP "%s"', $regex_path);
			$sth = self::$_record->prepare($sql);
			$sth->execute();
		}
		
		return TRUE;
	}
	
	/**
	 * Check type
	 * 
	 * @param string
	 */
	public static function type($path) {
		$sth = self::$_record->prepare('SELECT type FROM files WHERE path=:path');
		$sth->execute(array(':path' => $path));
		$result = $sth->fetch();
		
		return isset($result['type']) ? $result['type'] : NULL;
	}
	
	/**
	 * Check path is dir or not
	 * 
	 * @param string
	 */
	public static function isDir($path) {
		$sth = self::$_record->prepare('SELECT type FROM files WHERE path=:path AND type="dir"');
		$sth->execute(array(':path' => $path));
		
		return $sth->fetch() != NULL;
	}
	
	/**
	 * Check path is file or not
	 * 
	 * @param string
	 */
	public static function isFile($path) {
		$sth = self::$_record->prepare('SELECT type FROM files WHERE path=:path AND type="file"');
		$sth->execute(array(':path' => $path));
		
		return $sth->fetch() != NULL;
	}
	
	/**
	 * Check file or directory is exists
	 * 
	 * @param string
	 */
	public static function isExists($path) {
		$sth = self::$_record->prepare('SELECT COUNT(path) FROM files WHERE path=:path');
		$sth->execute(array(':path' => $path));
		$result = $sth->fetch();
		
		return $result[0] != 0;
	}
	
	/**
	 * Check whether an error occurred
	 * 
	 * @param boolean / integer
	 */
	public static function isError($path) {
		$result = $_is_error;
		self::$_is_error = false;
		return $result;
	}
	
	/**
	 * Get Used Capacity
	 * 
	 * @param string
	 */
	public static function getUsed() {
		$sth = self::$_record->query('SELECT size FROM files WHERE type="file"');
		$result = $sth->fetch();

		return isset($result[0]) ? (int)$result[0] : 0;
	}
}
