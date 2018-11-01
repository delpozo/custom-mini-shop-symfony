<?php
/**
 * Created by PhpStorm.
 * User: Poiz Campbell
 */

namespace App\CodePool\ProductHelpers;

use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

require_once __DIR__    . "/../../CodePool/_DEFINITIONS_.php";
require_once __DIR__    . "/../../../config/pimple_config.php";

abstract class ClassInitializer {

	/**
	 * @var Session
	 */
	protected static $session;

	/**
	 * @var Connection
	 */
	protected static $db;

	/**
	 * @var EntityManager
	 */
	protected static $eMan;



	private static function initClass(){
		if(!self::$db){
			self::$eMan     = $GLOBALS['E_MAN'];
			self::$db       = self::$eMan->getConnection();
		}
		$session_factory = new SessionFactory;
		static::$session = $session_factory->newInstance($_COOKIE);
		if (session_status() == PHP_SESSION_NONE) {
			if(!static::$session->isStarted()){
				static::$session->start();
			}
		}
		# static::$session->destroy();
	}

	/**
	 * @return Connection
	 */
	public static function getDb() {
		self::initClass();
		return self::$db;
	}

	/**
	 * @return Session
	 */
	public static function getSession() {
		self::initClass();
		return self::$session;
	}

	public static function get_next_possible_column_value($tbl='category', $col_name='ordering'){
		self::initClass();
		$sql        = "SELECT MAX(tbl.{$col_name}) FROM {$tbl} AS tbl";
		$next_val   = self::$db->executeQuery($sql)->fetch(\PDO::PARAM_INT);
		$next_val   = $next_val ? ( intval($next_val) + 1 ) : 1;
		return $next_val;
	}

	public static function get_all_table_columns_as_array($table_name='attributes'){
		self::initClass();
		$db_name        = self::$db->getDatabase();
		$array_of_cols  = array();

		$sql=<<<QRY
SELECT `COLUMN_NAME` AS cn
FROM `INFORMATION_SCHEMA`.`COLUMNS`
WHERE `TABLE_SCHEMA`='{$db_name}'
    AND `TABLE_NAME`='{$table_name}';
QRY;

		$statementObj   = self::$db->executeQuery($sql);
		$result =$statementObj->fetchAll(\PDO::FETCH_ASSOC);

		if($result){
			foreach($result as $intDex=>$objCol){
				if(@$objCol->cn == 'id'){
					continue;
				}
				$array_of_cols[]    = $objCol->cn;
			}
		}
		return $array_of_cols;
	}

	public static function drop_table_column($column_name=null, $table_name='attributes'){
		self::initClass();
		$sql            = self::$db->getQuery(true);
		$sql            = "ALTER TABLE " . self::$db->quoteName(trim($table_name)) . " DROP COLUMN " . $column_name;
		self::$db->setQuery($sql);
		return  self::$db->execute();
	}

	public static function change_table_column(array $properties, $table_name='attributes'){
		self::initClass();
		$changes        = implode("  ", $properties);
		$sql            = self::$db->getQuery(true);
		$sql            = "ALTER TABLE " . self::$db->quoteName(trim($table_name)) . " CHANGE " . $changes;
		self::$db->setQuery($sql);
		return  self::$db->execute();
	}

	public static function modify_table_column(array $properties, $table_name='attributes'){
		self::initClass();
		$modification   = implode("  ", $properties);
		$sql            = self::$db->getQuery(true);
		$sql            = "ALTER TABLE " . self::$db->quoteName(trim($table_name)) . " MODIFY " . $modification;
		self::$db->setQuery($sql);
		return  self::$db->execute();
	}

	public static function add_table_column($column_name, array $properties, $table_name='attributes'){
		self::initClass();
		//ALTER TABLE yourtable ADD q6 VARCHAR( 255 ) after q5
		$definition     = implode("  ", $properties);
		$sql            = self::$db->getQuery(true);
		$sql            = "ALTER TABLE " . self::$db->quoteName(trim($table_name)) . " ADD " . $column_name . " " . $definition;
		self::$db->setQuery($sql);
		return  self::$db->execute();
	}

	public static function get_column_definition($column_name, $table_name='attributes'){
		self::initClass();
		$sql            = self::$db->getQuery(true);
		$table_schema   = self::$registry->get('database');
		$sql            =<<<TDEF
SELECT  CAST(COLUMN_NAME AS CHAR)           AS col_name,
		CAST(COLUMN_TYPE AS CHAR)           AS col_type,
		CAST(CHARACTER_SET_NAME AS CHAR)    AS col_charset,
		CAST(COLLATION_NAME AS CHAR)        AS col_collation,
		CAST(COLUMN_DEFAULT AS CHAR)        AS col_default,
		CAST(IS_NULLABLE AS CHAR)           AS col_nullable
 FROM INFORMATION_SCHEMA.COLUMNS
WHERE  TABLE_SCHEMA = '{$table_schema}'
   AND TABLE_NAME = '{$table_name}'
   AND COLUMN_NAME = '{$column_name}';
TDEF;

		self::$db->setQuery($sql);
		self::$db->execute();
		$result = self::$db->loadObject();
		return $result;
		//select COLUMN_TYPE from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{database name}' AND TABLE_NAME = '{table name}' AND COLUMN_NAME = '{column name}';
	}

	public static function get_full_column_definition(){
		$col_def_arr        = array();
		$existing_columns   = self::get_all_table_columns_as_array();
		foreach($existing_columns as $col_name){
			$col_def_arr[$col_name] = self::get_column_definition($col_name);
		}
		return $col_def_arr;
	}


} 