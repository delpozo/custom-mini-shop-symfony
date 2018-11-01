<?php
/**
 * Created by PhpStorm.
 * User: Poiz Campbell
 */

namespace App\CodePool\ProductHelpers;

use Aura\Session\Segment;
use Aura\Session\Session;
use App\CodePool\ProductHelpers\CSCartHelper        as CSCH;
use App\CodePool\ProductHelpers\ClassInitializer    as CLINIT;

class CSOrderManager {

	protected $cat_id;
	protected $prod_id;
	protected static $attrib_id;

	protected static $product_store;        //AN ARRAY HOLDING ALL ORDERS KEYED WITH A CONCATENATION OF BOTH CAT_ID AND SUB-KEYED WITH PROD_ID FOR EASIER/FASTER ACCESS
	protected static $product_strip;
	/**
	 * @var Session
	 */
	protected static $store_session;

	/**
	 * @var Segment
	 */
	protected static $segment;
	protected static $order_context_obj ;


	public static function initialize_store(){
		if(!self::$store_session){
			self::$store_session    = CLINIT::getSession();
		}
		self::$segment = self::$store_session->getSegment('poiz\me\shop');

		if( !self::$segment->get('cs_shop_order_data') ){
			self::$segment->set("cs_shop_order_data", array());
		}

		if(!self::$product_store){
			//CREATE A UNIQUELY NAME-SPACED SESSION OBJECT FOR STORING ORDER DATA
			self::$product_store    = self::$segment->get("cs_shop_order_data", array());
		}
		//RETURN THE PRODUCT STORE ARRAY FOR CHAINING...
		return self::$product_store;
	}

	public static function remove_order_to_store($cat_id, $prod_id, $attr_id, $price, $qty=1, $discount_id=null){
		self::$product_store        = self::initialize_store();
		$order_strip                = new \stdClass();
		$order_strip->attrib_id     = $attr_id;
		$order_strip->discount_id   = $discount_id;
		$order_strip->prod_id       = $prod_id;
		$order_strip->cat_id        = $cat_id;
		$order_strip->qty           = $qty;
		$order_strip->u_price       = floatval($price);
		$order_strip->cnxt_total    = floatval($price);

		if(array_key_exists($cat_id,  self::$product_store)){
			//CALL A SUB-ROUTINE TO PROCESS ADDING A ORDER TO THE PRODUCT STORE...
			self::$product_store    = self::process_order($cat_id, $attr_id, $order_strip, 1);
		}else{
			//SIMPLE CREATE IT IMMEDIATELY IF IT DOES NOT ALREADY EXIST IN THE STORE
			self::$product_store[$cat_id][$attr_id]   = $order_strip;
			self::$segment->set("cs_shop_order_data", self::$product_store);
		}
		self::$product_store;
		return self::$product_store;
	}

	public static function add_order_to_store($cat_id, $prod_id, $attr_id, $price, $qty=1, $discount_id=null){
		self::$product_store        = self::initialize_store();
		$order_strip                = new \stdClass();
		$order_strip->attrib_id     = $attr_id;
		$order_strip->discount_id   = $discount_id;
		$order_strip->prod_id       = $prod_id;
		$order_strip->cat_id        = $cat_id;
		$order_strip->qty           = $qty;
		$order_strip->u_price       = floatval($price);
		$order_strip->total         = floatval($price * $qty);
		$order_strip->ord_time      = time();
		
		if(!$attr_id) return null;

		if( @array_key_exists($cat_id,  self::$product_store) ){
			//LEVEL #1  :: IST KEY AS CAT_ID
			if( @array_key_exists($prod_id,  self::$product_store[$cat_id]) ){
				//LEVEL #2 :: 2ND KEY AS PROD_ID
				if( @array_key_exists($attr_id,  self::$product_store[$cat_id][$prod_id]) ){
					self::$product_store    = self::process_order($cat_id, $prod_id, $attr_id, $order_strip);
				}else{
					//LEVEL #3 :: 3RD KEY AS ATTR_ID
					self::$product_store[$cat_id][$prod_id][$attr_id]   = $order_strip;
					self::$segment->set("cs_shop_order_data", self::$product_store);
				}
			}else{
				//LEVELS #2 & #3 COMBINED IN ONE GO
				self::$product_store[$cat_id][$prod_id][$attr_id]       = $order_strip;
				self::$segment->set("cs_shop_order_data", self::$product_store);
			}
		}else{
			//SIMPLE CREATE IT IMMEDIATELY IF IT DOES NOT ALREADY EXIST IN THE STORE
			//LEVELS #2 & #3 COMBINED IN ONE GO
			self::$product_store[$cat_id][$prod_id][$attr_id]           = $order_strip;
			self::$segment->set("cs_shop_order_data", self::$product_store);
		}

		return self::get_context_payload($cat_id, $prod_id, $attr_id);
	}

	public static function delete_all_of_same_aid($cat_id, $prod_id, $attr_id, $price, $qty=1, $discount_id=null){
		self::$product_store        = self::initialize_store();
		$order_strip                = new \stdClass();
		$order_strip->attrib_id     = $attr_id;
		$order_strip->discount_id   = $discount_id;
		$order_strip->prod_id       = $prod_id;
		$order_strip->cat_id        = $cat_id;
		$order_strip->qty           = $qty;
		$order_strip->u_price       = floatval($price);
		$order_strip->total         = floatval($price);

		//LEVEL #1  :: IST KEY AS CAT_ID
		if( @array_key_exists($cat_id,  self::$product_store) ){
			//LEVEL #2 :: 2ND KEY AS PROD_ID
			if( @array_key_exists($prod_id,  self::$product_store[$cat_id]) ){
				//LEVEL #3 :: 3RD KEY AS ATTRIB_ID
				if( @array_key_exists($attr_id,  self::$product_store[$cat_id][$prod_id]) ){
					unset(self::$product_store[$cat_id][$prod_id][$attr_id]);
					self::save_to_store_session(self::$product_store);
				}
			}
		}
		return CSCH::render_cart();  // ($a = self::get_context_payload($cat_id, $prod_id, $attr_id) )? $a : null;
	}

	public static function delete_order_from_store($cat_id, $prod_id, $attr_id, $price, $qty=1, $discount_id=null){
		self::$product_store        = self::initialize_store();
		$order_strip                = new \stdClass();
		$order_strip->attrib_id     = $attr_id;
		$order_strip->discount_id   = $discount_id;
		$order_strip->prod_id       = $prod_id;
		$order_strip->cat_id        = $cat_id;
		$order_strip->qty           = $qty;
		$order_strip->u_price       = floatval($price);
		$order_strip->total         = floatval($price * $qty);
		$order_strip->ord_time      = time();

		if( @array_key_exists($cat_id,  self::$product_store) ){
			//LEVEL #1  :: IST KEY AS CAT_ID
			if( @array_key_exists($prod_id,  self::$product_store[$cat_id]) ){
				//LEVEL #2 :: 2ND KEY AS PROD_ID
				if( @array_key_exists($attr_id,  self::$product_store[$cat_id][$prod_id]) ){
					self::$product_store    = self::process_order($cat_id, $prod_id, $attr_id, $order_strip, 0);
				}
			}
		}
		return ($a = self::get_context_payload($cat_id, $prod_id, $attr_id) )? $a : null;
	}

	protected static function process_order($cat_id, $prod_id, $attr_id, $order_strip, $add_or_remove=1){
		self::$product_store        = self::initialize_store();
		self::$order_context_obj    = &self::$product_store[$cat_id][$prod_id];

		// LOOP THROUGH THE ALREADY-EXISTING CONTEXT-PAYLOAD AND SEE IF
		// AN OBJECT WITH THE AN ATTRIBUTE OF $order_strip->attrib_id EXISTS IN IT
		// IF IT DOES;  THEN DO THE MATH OF INCREMENTING THE QUANTITY AS THE TOTAL FOR THE THE CONTEXT.
		// ONCE YOU ARE DONE, SAVE THE RESULTS BACK TO THE PRODUCT STORE AND EQUALLY MAKE THE CHANGES TO
		// THE SESSION OBJECT: (cs_shop_order_data -- com_cs_shop
		if( @array_key_exists($attr_id, self::$order_context_obj)  ){
			//ARE WE ADDING OR REMOVING???  || ADD = 1, REMOVE = 0
			$orderObj   = &self::$order_context_obj[$attr_id];
			if($add_or_remove){
				if($order_strip->qty > 0){
					$orderObj->ord_time     = time();
					$orderObj->qty          = $orderObj->qty            + intval($order_strip->qty);
					$orderObj->total        = $orderObj->total          + (float)( (float)$order_strip->u_price * (int)$order_strip->qty );
					self::$product_store[$cat_id][$prod_id][$attr_id]   = $orderObj;
				}
			}else{
				// TRACK & ABORT NEGATIVES:
				if( ( intval($orderObj->qty) - intval($order_strip->qty) ) <= 0){
					unset(self::$product_store[$cat_id][$prod_id][$attr_id]);
					if( empty(self::$product_store[$cat_id]) ){
						unset(self::$product_store[$cat_id][$prod_id]);
						unset(self::$product_store[$cat_id]);
					}else if( empty(self::$product_store[$cat_id][$prod_id]) ){
						unset(self::$product_store[$cat_id][$prod_id]);
					}
				}else{
					$orderObj->qty     = $orderObj->qty       - intval($order_strip->qty);
					$orderObj->total   = $orderObj->total     - (float)( (float)$order_strip->u_price * (int)$order_strip->qty );
					$orderObj->ord_time= time();
					self::$product_store[$cat_id][$prod_id][$attr_id]             = $orderObj;
				}
			}
		}

		self::save_to_store_session(self::$product_store);
		return self::$product_store;
	}

	private static function save_to_store_session($data){
		self::$segment->set("cs_shop_order_data", $data);
	}

	private static function get_from_store_session(){
		self::$product_store    = self::initialize_store();
		return self::$segment->get("cs_shop_order_data", null);
	}

	public static function get_context_payload($cat_id, $prod_id, $attr_id){
		$context_total          = 0.00;
		$context_qty            = 0;
		self::$product_store    = self::initialize_store();

		if( !isset(self::$product_store[$cat_id][$prod_id])){
			return null;
		}

		$context_obj_ar     =  @self::$product_store[$cat_id][$prod_id];

		if(count($context_obj_ar) > 0){
			foreach($context_obj_ar as $key=>$obj){
				$context_total  += floatval($obj->total);
				$context_qty    += intval($obj->qty);
			}
			foreach($context_obj_ar as $key=>$obj){
				$obj->cxt_total = $context_total;
				$obj->cxt_qty   = $context_qty;
			}
			if( isset($context_obj_ar[$attr_id])){
				return $context_obj_ar[$attr_id];
			}
		}
		return null;
	}

	public static function getTotalAndQuantity($cid, $pid, $aid){
		$contextPayload     = CSOrderManager::get_context_payload($cid, $pid, $aid);
		$objResult          = new \stdClass();
		$objResult->qty     = 0;
		$objResult->total   = '0.00';
		if(isset($contextPayload)){
			$objResult      = $contextPayload;
		}
		return $objResult;
	}

	public static function get_product_level_order_payload($cat_id, $prod_id){
		self::$product_store    = self::initialize_store();
		return self::$product_store[$cat_id][$prod_id];
	}

	public static function get_attribute_level_order_payload($cat_id, $prod_id, $attr_id){
		self::$product_store    = self::initialize_store();
		return self::$product_store[$cat_id][$prod_id][$attr_id];
	}

	public static function get_global_order_payload(){
		self::$product_store    = self::initialize_store();
		return self::$product_store;
	}

	public static function destroy_order_data(){
		if( self::$segment->get("cs_shop_order_data") ){
			self::$product_store = array();
			self::$store_session->clear();
			return true;
		}
		return false;
	}
}