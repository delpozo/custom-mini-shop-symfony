<?php
/**
 * Created by PhpStorm.
 * User: Poiz Campbell
 */

namespace App\CodePool\ProductHelpers;
use Aura\Session\Segment;
use Aura\Session\Session;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use App\CodePool\ProductHelpers\ClassInitializer    as CLINIT;


require_once __DIR__    . "/../_DEFINITIONS_.php";


class CSRoutineHelper {
	/**
	 * @var Session
	 */
	protected static $session;

	/**
	 * @var Connection
	 */
	protected static $db;

	/**
	 * @var Segment
	 */
	protected static $segment;

	/**
	 * @var EntityManager
	 */
	protected static $eMan;

	protected static function initClass(){
		self::$db       = CLINIT::getDb();
		self::$session  = CLINIT::getSession();
	}
	
	public static function get_thumb_equiv($full_img_path){
		$file_parts = preg_split("#\/|\\\#", $full_img_path);
		$file_name  = array_pop($file_parts);
		$thumb_path = implode("/", $file_parts) . "/thumbs/";
		$thumb_file = $thumb_path . $file_name;
		return $thumb_file;
	}
	
	public static function get_rgb_transpose($color){
		$colors_ar["BLACK"]     = "rgba(0, 0, 0, 1)";
		$colors_ar["BLUE"]      = "rgba(0, 0, 170, 1)";
		$colors_ar["BROWN"]     = "rgba(125, 68, 40, 1)";
		$colors_ar["SILVER"]    = "rgba(191, 192, 191, 1)";;
		$colors_ar["GOLDEN"]    = "rgba(150, 107, 36, 1)";
		$colors_ar["GREEN"]     = "rgba(0, 170, 0, 1)";
		$colors_ar["ORANGE"]    = "rgba(233, 131, 0, 1)";
		$colors_ar["PINK"]      = "rgba(255, 13, 159, 1)";
		$colors_ar["YELLOW"]    = "rgba(255, 216, 0, 1)";
		$colors_ar["WHITE"]     = "rgba(254, 255, 253, 1);text-shadow: 1px 1px 1px rgba(210, 161, 111, 0.95);";
		$colors_ar["NONE"]      = "rgba(255, 255, 255, 0.01);text-shadow: 1px 1px 1px rgba(210, 161, 111, 0.95);";
		$colors_ar["--"]        = "rgba(255, 255, 255, 0.01);text-shadow: 1px 1px 1px rgba(210, 161, 111, 0.95);";
		$colors_ar["RED"]       = "rgba(190, 0, 0, 1)";
		$colors_ar["GRAY"]      = "rgba(147, 148, 147, 1)";
		$colors_ar["ASH"]       = "rgba(191, 192, 191, 1)";
		$colors_ar["OTHER"]     = "rgba(0, 0, 255, 1)";
		$colors_ar["RAINBOW"]   = "background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(top,  #ff0000 0%, #ff7423 15%, #ffff00 32%, #00ff00 47%, #0000ff 64%, #882ffc 83%, #c170ff 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ff0000), color-stop(15%,#ff7423), color-stop(32%,#ffff00), color-stop(47%,#00ff00), color-stop(64%,#0000ff), color-stop(83%,#882ffc), color-stop(100%,#c170ff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ff0000 0%,#ff7423 15%,#ffff00 32%,#00ff00 47%,#0000ff 64%,#882ffc 83%,#c170ff 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ff0000 0%,#ff7423 15%,#ffff00 32%,#00ff00 47%,#0000ff 64%,#882ffc 83%,#c170ff 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ff0000 0%,#ff7423 15%,#ffff00 32%,#00ff00 47%,#0000ff 64%,#882ffc 83%,#c170ff 100%); /* IE10+ */
background: linear-gradient(to bottom,  #ff0000 0%,#ff7423 15%,#ffff00 32%,#00ff00 47%,#0000ff 64%,#882ffc 83%,#c170ff 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#c170ff',GradientType=0 ); /* IE6-9 */";
		$colors_ar["MIXED"]     = $colors_ar["RAINBOW"];
		
		if( isset ($colors_ar[$color]) ){
			return $colors_ar[$color];
		}
		return $colors_ar["BLACK"];
	}
	
	public static function fetch_sku($prod_id){
		self::initClass();
		$sql    = "SELECT  pdt.sku  FROM " . TBL_PRODUCTS . " AS pdt ";
		$sql   .= "WHERE pdt.id=:P_ID ";
		$stm    = self::$db->prepare($sql);
		$stm->execute(['P_ID'=>$prod_id]);
		
		return $stm->fetchColumn();
	}
	
	public static function generateUpperCasedHexHash($length=18) {
		$characters = '0123456789ABCDEF';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		$returnable  = "";
		$returnable .= $randomString;
		return $returnable;
	}

} 