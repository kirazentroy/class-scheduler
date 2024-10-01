<?php
$globalConnection = 'sqlite';
if (!defined('GLOBAL_CONNECTION')) {
	define('GLOBAL_CONNECTION', $globalConnection);
}

if (GLOBAL_CONNECTION === 'sqlite') {
	if (!defined('DB_FILE')) {
		define('DB_FILE', '/home/ascblzri/class-scheduler.asc-bislig.com/database/database.sqlite');
	}
	
	if (!file_exists(DB_FILE)) {
		$currentDirectory = shell_exec('pwd');
		echo "Current Directory: " . $currentDirectory . "<br/>";
		die("Database file does not exist: " . DB_FILE);
	}
}

if (!isset($connect) || !$connect) {
	if (GLOBAL_CONNECTION === 'sqlite') {
		$connect = new SQLite3(DB_FILE);
	} else if (GLOBAL_CONNECTION === 'mysqli') {
		$connect = new mysqli("localhost", "ascblzri_root", "troythesisP@$$", "ascblzri_scheduler");
	} else {
		$connect = new mysqli("localhost", "root", "", "scheduler");
	}
}

if (!function_exists('setForeignKeyChecks')) {
	function setForeignKeyChecks($connection, $enable)
	{
		if (GLOBAL_CONNECTION == 'mysqli') {
			$value = $enable ? 1 : 0;
			return $connection->query("SET FOREIGN_KEY_CHECKS = $value;");
		}
		else if (GLOBAL_CONNECTION == 'sqlite') {
			$value = $enable ? 'ON' : 'OFF';
			return $connection->exec("PRAGMA foreign_keys = $value;");
		} else {
			return false;
		}
	}
}

if (!function_exists('executeNonQuery')) {
	function executeNonQuery($connection, $sql)
	{
		if (GLOBAL_CONNECTION == 'mysqli') {
			return $connection->query($sql);
		}
		elseif (GLOBAL_CONNECTION == 'sqlite') {
			if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\s/i', $sql)) {
				$data = $connection->query($sql);
				return $data;
			} else {
				if ($connection->exec($sql)) {
					return true;
				}
				return false;
			}
		} else {
			return false;
		}
	}
}




if (!function_exists('fetchAssoc')) {
	function fetchAssoc($connection, &$result)
	{
		if (GLOBAL_CONNECTION == 'mysqli') {
			return mysqli_fetch_assoc($result);
		}
		elseif (GLOBAL_CONNECTION == 'sqlite') {
			return $result->fetchArray(SQLITE3_ASSOC);
		} else {
			return false;
		}
	}
}

if (!function_exists('numRows')) {
	function numRows($connection, &$result)
	{
		if (GLOBAL_CONNECTION == 'mysqli') {
			return mysqli_num_rows($result);
		} elseif (GLOBAL_CONNECTION == 'sqlite') {
			$data = $result;
			$rows = [];
			while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
				$rows[] = $row;
			}
			return count($rows);
		}
		return 0;
	}
}

if (!function_exists('getBaseUrl')) {
	function getBaseUrl()
	{
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
		$host = $_SERVER['HTTP_HOST'];
		return $protocol . $host . '/';
	}
}

if (!function_exists('fetchNumeric')) {
	function fetchNumeric($connection, $result)
	{
		if (GLOBAL_CONNECTION == 'mysqli') {
			return mysqli_fetch_array($result, MYSQLI_NUM);
		}
		elseif (GLOBAL_CONNECTION == 'sqlite') {
			return $result->fetchArray(SQLITE3_NUM);
		} else {
			return false;
		}
	}
}

if (!$connect) {
	header('location: '.getBaseUrl().'error.php');
}
