<?php
/**
 * RNFileSystem Virtual File Layer with Parliament DFS
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

class VirDFS {
	
	/**
	 * @var string
	 */
	private static $_username;
	
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
	public static function init($config) {
		if(!class_exists('PDO'))
			throw new Exception('PDO is not exists.');
		
		// Local root
		self::$_root = $config['root'];
		
		// Files revert revision
		if(isset($config['$revert']) && NULL != $config['revert'])
			self::$_revert = $config['$revert'];
		
		// Database table postfix
		self::$_username = $config['username'];
		
		// Make root folder
		if(!file_exists(self::$_root . '/data'))
			mkdir(self::$_root . '/data', 0755, TRUE);

		$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $config['host'], $config['port'], $config['name']);
		self::$_record = new PDO($dsn, $config['user'], $config['pass']);
		
		self::$_record->query("SET NAMES 'utf8'");
		self::$_record->query("SET CHARACTER_SET_CLIENT=utf8");
		self::$_record->query("SET CHARACTER_SET_RESULTS=utf8");
		
		// Create files table
		self::$_record->query(
			'CREATE TABLE IF NOT EXISTS files_' . self::$_username . ' (' .
				'id varchar(32) NOT NULL,' .
				'path TEXT NOT NULL,' .
				'type VARCHAR(4) NOT NULL,' .
				'size INT(10),' .
				'hash VARCHAR(32),' .
				'time INT(10),' .
				'mime VARCHAR(16),' .
				'version INT(10),' .
				'revision INT(10),' .
				'unique_hash VARCHAR(16),' .
				'PRIMARY KEY (id)' .
			') ENGINE=INNODB DEFAULT CHARSET=utf8;'
		);
		
		if(!self::isExists('/')) {
			$sth = self::$_record->prepare('INSERT INTO files_' . self::$_username . ' (id, path, type) VALUES ("6666cd76f96956469e7be39d750cc7d9", "/", "dir")');
			$sth->execute(array(':username' => self::$_username));
		}
	}
	
	/**
	 * Mess String
	 */
	private static function messString($length = 16) {
		$char = array(
			'1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
			'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
			'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
			'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
			'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
			'Y', 'Z'
		);
		$str = '';
		do {
			$str .= $char[rand() % 62];
		} while(strlen($str) < $length);
		return $str;
	}
	
	/**
	 * Index Files
	 */
	public static function index($path = '/') {
		if('/' === $path) {
			$list = array();
			$sth = self::$_record->query('SELECT path,type,size,hash,time,mime,version FROM files_' . self::$_username);
			while($result = $sth->fetch()) {
				if('file' == $result['type']) {
					$list[$result['path']] = array(
						'type' => 'file',
						'size' => $result['size'],
						'hash' => $result['hash'],
						'time' => $result['time'],
						'mime' => $result['mime'],
						'version' => $result['version']
					);
				}
				else
					$list[$result['path']] = array('type' => 'dir');
			}
		}
		else {
			if(!self::isExists($path))
				return NULL;
			
			$list = NULL;
			
			$sth = self::$_record->prepare('SELECT path,type,size,hash,time,mime,version FROM files_' . self::$_username . ' WHERE id=:id');
			$sth->execute(array(':id' => md5($path)));
			$result = $sth->fetch();
			
			if('file' == $result['type'])
				return $list[$result['path']] = array(
					'type' => 'file',
					'size' => $result['size'],
					'hash' => $result['hash'],
					'time' => $result['time'],
					'mime' => $result['mime'],
					'version' => $result['version']
				);
			else
				$list[$result['path']] = array('type' => 'dir');
			
			$regex_path = sprintf('^\/%s\/', str_replace('/', '\/', trim($path, '/')));
			$sql = sprintf('SELECT path,type,size,hash,time,mime,version FROM files_' . self::$_username . ' WHERE path REGEXP "%s"', $regex_path);
			$sth = self::$_record->prepare($sql);
			$sth->execute();
			
			while($result = $sth->fetch()) {
				if('file' == $result['type'])
					$list[$result['path']] = array(
						'type' => 'file',
						'size' => $result['size'],
						'hash' => $result['hash'],
						'time' => $result['time'],
						'mime' => $result['mime'],
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
	// public static function revert($path, $version = NULL) {
	// 	// Check file is exists
	// 	if(!self::isExists($path))
	// 		return FALSE;
		
	// 	if(NULL == $version)
	// 		return FALSE;
		
	// 	// Check File version is exists
	// 	$sth = self::$_record->prepare('SELECT * FROM files_' . self::$_username . ' WHERE id=:id');
	// 	$sth->execute(array(':id' => md5($path)));
	// 	$result = $sth->fetch();
		
	// 	// Check file version exists
	// 	if($result['version'] < $version && $version < 0)
	// 		return FALSE;
		
	// 	$result['revision'] = json_decode($result['revision'], TRUE);
		
	// 	// Delete new version
	// 	for($i = count($result['revision'])-1;$i > $version;--$i) {
	// 		$hash = array_pop($result['revision']);
	// 		unlink(self::$_root . '/data/' . $hash);
	// 	}
		
	// 	$sth = self::$_record->prepare('UPDATE files_' . self::$_username . ' SET version=:version, revision=:revision WHERE id=:id');
	// 	$sth->execute(array(
	// 		':id' => md5($path),
	// 		':version' => $version,
	// 		':revision' => json_encode($result['revision'])
	// 	));

	// 	return TRUE;
	// }
	
	/**
	 * Move File
	 * 
	 * @param string
	 * @param string
	 */
	public static function move($sim_src, $sim_dest) {
		// Check Sim Source and Sim Destination
		if(!self::isExists($sim_src) || self::isExists($sim_dest))
			return FALSE;
		
		// Change old path to new path
		$sth = self::$_record->prepare('UPDATE files_' . self::$_username . ' SET path=:new_path WHERE id=:id');
		$sth->execute(array(
			':id' => md5($sim_src),
			':new_path' => $sim_dest
		));
		
		// Create full directory path
		self::createFullDirPath($sim_dest, self::type($sim_src));
		
		if('dir' == self::type($sim_dest)) {
			// Load file path
			$regex_path_sql = sprintf('^\/%s\/', str_replace('/', '\/', trim($sim_src, '/')));
			$sql = sprintf('SELECT path FROM files_' . self::$_username . ' WHERE path REGEXP "%s"', $regex_path_sql);
			$sth = self::$_record->prepare($sql);
			$sth->execute();
			
			// Change sub directory to new path
			$regex_path = sprintf('/^\/%s\/(.*)/', str_replace('/', '\/', trim($sim_src, '/')));
			while($result = $sth->fetch()) {
				if(preg_match($regex_path, $result['path'], $match)) {
					$sth = self::$_record->prepare('UPDATE files_' . self::$_username . ' SET path=:new_path WHERE id=:id');
					$sth->execute(array(
						':id' => md5($result['path']),
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
				$unique_hash = self::messString();
			}
			while(Parliament::isExists(self::$_username . '_' . $unique_hash . '_0'));
			
			// Change File Permission
			chmod($real_path, 0777);

			// Copy Real File to Parliament DFS
			if(!Parliament::create(self::$_username . '_' . $unique_hash . '_0', $real_path))
				return FALSE;
			
			// Add new record
			$sth = self::$_record->prepare('INSERT INTO files_' . self::$_username . ' (id, path, type, size, hash, time, mime, version, revision, unique_hash) VALUES (:id, :path, :type, :size, :hash, :time, :mime, :version, :revision, :unique_hash)');
			$sth->execute(array(
				':id' => md5($sim_path),
				':path' => $sim_path,
				':type' => 'file',
				':size' => filesize($real_path),
				':hash' => hash_file('md5', $real_path),
				':time' => filectime($real_path),
				':mime' => mime_content_type($real_path),
				':version' => 0,
				':revision' => self::$_revert,
				':unique_hash' => $unique_hash
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
			if(!self::isExists($full_path)) {
				// Add new record
				$sth = self::$_record->prepare('INSERT INTO files_' . self::$_username . ' (id, path, type) VALUES (:id, :path, :type)');
				$sth->execute(array(
					':id' => md5($full_path),
					':path' => $full_path,
					':type' => 'dir'
				));
			}
			elseif('file' == self::type($full_path))
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

		$sth = self::$_record->prepare('SELECT * FROM files_' . self::$_username . ' WHERE id=:id');
		$sth->execute(array(':id' => md5($sim_path)));
		$result = $sth->fetch();
		
		// if file is same
		if(hash_file('md5', $real_path) == $result['hash'])
			return FALSE;


		// Need delete old version
		// FIXME


		// Change File Permission
		chmod($real_path, 0777);

		// Copy Real File to Parliament DFS
		$unique_id = self::$_username . '_' . $result['unique_hash'] . '_' . ($result['version']+1);
		if(!Parliament::create($unique_id, $real_path))
			return FALSE;

		$sth = self::$_record->prepare('UPDATE files_' . self::$_username . ' SET size=:size, hash=:hash, time=:time, mime=:mime, version=:version, revision=:revision WHERE id=:id');
		$sth->execute(array(
			':id' => md5($sim_path),
			':size' => filesize($real_path),
			':hash' => hash_file('md5', $real_path),
			':time' => filectime($real_path),
			':mime' => mime_content_type($real_path),
			':version' => $result['version']+1,
			':revision' => self::$_revert
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
		
		$sth = self::$_record->prepare('SELECT version,revision,mime,unique_hash FROM files_' . self::$_username . ' WHERE id=:id');
		$sth->execute(array(':id' => md5($path)));
		$result = $sth->fetch();
		
		// Check file version exists
		if(NULL !== $version) {
			if($result['version'] - $version > $result['revision'])
				return FALSE;
		}
		else
			$version = $result['version'];


		// Normal download
		if(NULL === $seek) {
			// ob_end_flush();
			header('Content-Type: ' . $result['mime']);
			Parliament::read(self::$_username . '_' . $result['unique_hash'] . '_' . $version);
			return TRUE;
		}
		
		// Resume download
		// FIXME
		// if(is_int($seek) && $seek <= filesize(self::$_root . '/data/' . $result['hash'][$version])) {
		// 	// ob_end_flush();
		// 	header('Content-Type: ' . mime_content_type(self::$_root . '/data/' . $result['revision'][$version]));
		// 	$handle = fopen(self::$_root . '/data/' . $result['revision'][$version], 'rb');
		// 	fseek($handle, $seek);
		// 	fpassthru($handle);
		// 	return TRUE;
		// }
		
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
			$sth = self::$_record->prepare('SELECT version,revision,unique_hash FROM files_' . self::$_username . ' WHERE id=:id');
			$sth->execute(array(':id' => md5($path)));
			$result = $sth->fetch();
			
			// Delete all file version
			for($index = $result['version'];$index >= ($result['version']-$result['revision']), $index >= 0;$index--)
				Parliament::delete(self::$_username . '_' . $result['unique_hash'] . '_' . $index);
			
			// Delete file record
			$sth = self::$_record->prepare('DELETE FROM files_' . self::$_username . ' WHERE id=:id');
			$sth->execute(array(':id' => md5($path)));
		}
		else {
			if('/' !== $path) {
				// Delete directory record
				$sth = self::$_record->prepare('DELETE FROM files_' . self::$_username . ' WHERE id=:id');
				$sth->execute(array(':id' => md5($path)));
				
				$regex_path = sprintf('^\/%s\/', str_replace('/', '\/', trim($path, '/')));
			}
			else
				$regex_path = '^\/.+';
			
			// Load all files record
			$sth = self::$_record->prepare('SELECT version,revision,unique_hash FROM files_' . self::$_username . ' WHERE id=:id AND type="file"');
			$sth->execute(array(':id' => md5($path)));
			
			// Delete all file version
			while($result = $sth->fetch()) {
				for($index = $result['version'];$index >= ($result['version']-$result['revision']), $index >= 0;$index--)
					Parliament::delete(self::$_username . '_' . $result['unique_hash'] . '_' . $index);
			}
			
			// Delete all file record
			$sql = sprintf('DELETE FROM files_' . self::$_username . ' WHERE path REGEXP "%s"', $regex_path);
			$sth = self::$_record->prepare($sql);
			$sth->execute();
		}
		
		return TRUE;
	}
	
	/**
	 * Get file information
	 * 
	 * @param string
	 */
	public static function info($path) {
		if(!self::isExists($path))
			return FALSE;
		
		$sth = self::$_record->prepare('SELECT type,size,hash,time,version FROM files_' . self::$_username . ' WHERE id=:id');
		$sth->execute(array(':id' => md5($path)));
		$result = $sth->fetch();
		
		if('file' == $result['type'])
			$info = array(
				'type' => $result['type'],
				'size' => $result['size'],
				'hash' => $result['hash'],
				'time' => $result['time'],
				'version' => $result['version']
			);
		else
			$info = array('type' => $result['type']);
		
		return $info;
	}
	
	/**
	 * Check type
	 * 
	 * @param string
	 */
	public static function type($path) {
		$sth = self::$_record->prepare('SELECT type FROM files_' . self::$_username . ' WHERE id=:id');
		$sth->execute(array(':id' => md5($path)));
		$result = $sth->fetch();
		
		return isset($result['type']) ? $result['type'] : NULL;
	}
	
	/**
	 * Check path is dir or not
	 * 
	 * @param string
	 */
	public static function isDir($path) {
		$sth = self::$_record->prepare('SELECT type FROM files_' . self::$_username . ' WHERE id=:id AND type="dir"');
		$sth->execute(array(':id' => md5($path)));
		
		return $sth->fetch() != NULL;
	}
	
	/**
	 * Check path is file or not
	 * 
	 * @param string
	 */
	public static function isFile($path) {
		$sth = self::$_record->prepare('SELECT type FROM files_' . self::$_username . ' WHERE id=:id AND type="file"');
		$sth->execute(array(':id' => md5($path)));
		
		return $sth->fetch() != NULL;
	}
	
	/**
	 * Check file or directory is exists
	 * 
	 * @param string
	 */
	public static function isExists($path) {
		$sth = self::$_record->prepare('SELECT path FROM files_' . self::$_username . ' WHERE id=:id');
		$sth->execute(array(':id' => md5($path)));
		
		return count($sth->fetchAll()) != 0;
	}
	
	/**
	 * Check whether an error occurred
	 * 
	 * @param boolean / integer
	 */
	public static function isError() {
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
		$sth = self::$_record->query('SELECT SUM(size) FROM files_' . self::$_username . ' WHERE type="file"');
		$result = $sth->fetch();

		return isset($result[0]) ? (int)$result[0] : 0;
	}
}
