<?php
	/**
	 * Created by JetBrains PhpStorm.
	 * User: Poiz Campbell
	 */

	namespace App\CodePool\ProductHelpers;
	
	require_once __DIR__    . "/../_DEFINITIONS_.php";
	
	use Aura\Session\Segment;
	use Aura\Session\Session;
	use Doctrine\DBAL\Connection;
	use Doctrine\ORM\EntityManager;
	
	use App\CodePool\ProductHelpers\CSRoutineHelper         as CSRH;
	use App\CodePool\ProductHelpers\CSOrderManager          as CSOM;
	use App\CodePool\ProductHelpers\ClassInitializer        as CLINIT;


class CSCartHelper{
	protected static $cs_cart;
	protected static $order_payload;
	protected static $order_collection;
	protected static $BASE_URI  = "/";
	protected static $VIEW_SPEC = 1;        //1 FOR GENERAL VIEWING AND 2 FOR RESTRICTED VIEWING.
	protected static $filters   = array();

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

	public static function render_cart($type='mini', $neutral=false, $as_collection=false){
		$pld    = CSOM::get_global_order_payload() ;
		$pool   = array();
		if($pld){
			foreach($pld as $intDex=>$cid_ar){
				if(is_array($cid_ar) ){
					foreach($cid_ar as $aid=>$pid_ar){
						if(is_array($pid_ar)){
							foreach($pid_ar as $obj){
								// HERE EACH $obj CONTAINS THE FOLLOWING: attrib_id, discount_id, prod_id, cat_id, qty, u_price, total, cxt_total, cxt_qty
								// THE IDEA WOULD BE THUS: TO GENERATE A WRAPPER HTML FOR EACH SINGLE ORDER OBJECT
								// AND THEN BUNDLE IT UP AS A SHOPPING CART
								$pool[] = self::obtain_cart_data_for_order($obj);
							}
						}
					}
				}
			}
		}
		return self::build_payload($pool, $type, $neutral, $as_collection);
	}

	protected static function initClass(){
		self::$db       = CLINIT::getDb();
		self::$session  = CLINIT::getSession();

		self::$segment = self::$session->getSegment('poiz\me\shop');

		if( !self::$segment->get('cs_shop_order_data') ){
			self::$segment->set("cs_shop_order_data", array());
		}
	}

	public static function array_to_obj($array){
		$obj = new \stdClass();
		if(!$array || empty($array)) return $array;
		foreach($array as $k=>$v){
			$obj->$k = $v;
		}
		return $obj;
	}

	public static function obtain_cart_data_for_order($obj){
		if(!$obj) return null;
		self::initClass();

		$params = [
			'P_ID' =>  intval($obj->prod_id),
			'A_ID' =>  intval($obj->attrib_id),
		];
		$sql    =  "SELECT pdt.id AS prod_id,       pdt.title AS prod_title,    pcat.title AS cat_title,    pcat.id AS cat_id,
	                pimg.pix,                       patr.id AS attrib_id,       patr.size,                  patr.size_unit,
	                pman.title AS man_title,        patr.color,                 pprx.normal_price AS price, patr.sku,           pcat.prefix ";
		$sql   .= " FROM "      . TBL_PRODUCTS      . " AS pdt ";
		$sql   .= " LEFT JOIN " . TBL_CATS          . " AS pcat ON pcat.id        = pdt.cat_id ";
		$sql   .= " LEFT JOIN " . TBL_ATTRIBUTES    . " AS patr ON patr.prod_id   = pdt.id ";
		$sql   .= " LEFT JOIN " . TBL_MAKERS        . " AS pman ON pman.id        = patr.manufacturer ";
		$sql   .= " LEFT JOIN " . TBL_PRICES        . " AS pprx ON pprx.prod_id   = pdt.id    AND patr.id = pprx.attrib_id ";
		$sql   .= " LEFT JOIN " . TBL_IMAGES        . " AS pimg ON pimg.prod_id   = pdt.id    AND patr.id = pimg.attrib_id ";
		$sql   .= " WHERE pdt.id    =:P_ID ";
		$sql   .= " AND patr.id     =:A_ID ";

		if($obj->cat_id) {
			$sql .= " AND pcat.id    =:C_ID ";
			$params['C_ID'] = (int)$obj->cat_id;
		}
		$sql   .= " GROUP BY patr.id ";
		$statement = self::$db->prepare($sql);
		$statement->execute($params);
		$result = $statement->fetch(\PDO::FETCH_OBJ);

		if($result){
			if(is_object($result)){
				$result->qty            	= $obj->qty;
				$result->total          	= $obj->total;
				$result->discount_id    	= $obj->discount_id;
				$result->ord_time       	= $obj->ord_time;
			  	$result->u_price	      	= $obj->u_price;
			    $result->title		      	= ($v = @$obj->title)			?$v:null;
			  	$result->sold_on_discount	= ($v = @$obj->sold_on_discount)?$v:null;
			  	$result->discount_price 	= ($v = @$obj->discount_price)	?$v:null;
			}
		}
		return $result;
	}
	
	public static function build_cart_display(){
	}

	public static function set_view_spec($view_spec){
		self::$VIEW_SPEC    = $view_spec;
	}

	public static function build_payload($order_payload, $mini_or_maxi_view='mini', $neutral=false, $as_collection=false, $bypass_icons=false){
		$cart_head_title            = "POIZ *SHOPPING* CART";
	  	$base_uri					= "./";
		$wrapper_class              = 'mini_box';
	  	$delivery_data_processor    = $base_uri . "processors/product_ajax_bridge.php";
		$print_processor            = $base_uri . "processors/product_ajax_bridge.php";
		$main_shop_url              = $base_uri . "/shop/products";
		$print_deal_icon            = "/images/icons/_32_by_32/log_sale_icon.png";
		$back_2_shop_icon           = $base_uri . "/images/icons/_32_by_32/back_2_shop_icon_2.png";
		$delivery_info_icon         = $base_uri . "/images/icons/_32_by_32/add_delivery_data.png";
		self::$order_collection     = array();
		$checkout_link              = "/en/shop/checkout";

		if(!$order_payload  || empty($order_payload) ){
			return null;
		}
		foreach($order_payload as $k=>$v){
			if(!$v){
				unset($order_payload[$k]);
			}
		}

		$render_output  = "";
		$i_counter      = 0;
		$total          = 0;
		usort($order_payload, 'self::cmp');

		foreach($order_payload as $intDex=>$obj_payload){
			$total += floatval($obj_payload->total);
			$klass  = ($i_counter%2 == 0)? "even_row": "odd_row";

			if($mini_or_maxi_view == "maxi"){
				$render_output .= self::get_prod_cart_strip_maxi($obj_payload, $obj_payload->prod_id, $klass);
				$wrapper_class  = "maxi_box";
			}else{
				$render_output .= self::get_prod_cart_strip($obj_payload, $obj_payload->prod_id, $klass);
				$wrapper_class  = "mini_box";
			}
			self::$order_collection[]  = self::get_normlized_order($obj_payload, $obj_payload->prod_id, $klass);
			$i_counter++;
		}
		self::$order_collection['total'] = $total;
		$total          = self::normalize_price_val($total);
		$order_title    = "<span id='my_cart_txt' class='my_cart_txt'>{$cart_head_title}</span><span class='pull-right glyphicon glyphicon-minus'></span>";
		$wrapper        = "<div class='col-md-12 no_padding' >";
		$wrapper       .= "<h4 class='prod_cart_header col-md-12 order_heading'>{$order_title}</h4>";

		if($mini_or_maxi_view != "maxi"){
			$render_output  = $wrapper . $render_output.  "</div>";
		}
		$render_output .= "<div class='col-md-12 no_padding {$wrapper_class}' >";
		$render_output .= "<div class='prod_cart_footer col-md-12 order_heading'>";
		if($mini_or_maxi_view != "maxi") {
			if (self::$VIEW_SPEC == 2) {
				$render_output .= "<img src='{$print_deal_icon}'    id='close_n_print_shop_order'  class='close_n_print_shop_order linked_close_deal_icon' data-tip='À la<br /><strong> Caisse.</strong>' data-processor='{$print_processor}' />\n";
				$render_output .= "<img src='{$delivery_info_icon}' id='notes_icon'  class='notes_icon' data-tip='Bermerkung<br /><strong>schreiben</strong>' data-processor='{$delivery_data_processor}' />\n";
			}else{
				$render_output .= "<a href='{$checkout_link}' class='pz-checkout-icon' id='pz-checkout-icon'><img src='{$print_deal_icon}' id='check_out_icon'  class='check_out_icon check_out_icon' data-tip='À la<br /><strong> Caisse.</strong>' data-processor='{$print_processor}' /></a>\n";
			}
		}else{
			if(!$bypass_icons){
				$render_output .= "<a href='{$checkout_link}' class='pz-checkout-icon' id='pz-checkout-icon'><img src='{$print_deal_icon}' id='check_out_icon'  class='check_out_icon check_out_icon' data-tip='À la<br /><strong> Caisse.</strong>' data-processor='{$print_processor}' /></a>\n";
				$render_output .= "<a href='{$main_shop_url}' class='pizza-shop-link'><img src='{$back_2_shop_icon}' id='continue_shopping_icon'  class='continue_shopping_icon' data-tip='Retournez au <br /><strong>Shop</strong>' data-processor='{$delivery_data_processor}' /></a>\n";
			}
		}
		$render_output .= "<span class='cart_footer_total'>" . $total . "</span>\n";
		$render_output .= "</div>\n";
		$render_output .= "</div>\n";
		$render_output .= "<div style='clear:both;'>&nbsp;</div>";

		if($as_collection){
			return self::$order_collection;
		}
		return $render_output;
	}

	private static function cmp($a, $b)	{
		return ($a->ord_time < $b->ord_time);
	}

	private	static function get_order_type($prod_id){
		self::initClass();
		$sql    = "SELECT t_cat.alias FROM " . TBL_CATS . " AS t_cat ";
		$sql   .= "LEFT JOIN " . TBL_PRODUCTS . " AS t_prod ON t_cat.id = t_prod.cat_id ";
		$sql   .= "WHERE  t_prod.id =:P_ID ";
		$params = ['P_ID' =>  intval($prod_id)];
		$stm    = self::$db->prepare($sql);
		$stm->execute($params);
		return $stm->fetch(\PDO::FETCH_COLUMN);
	}

	public static function get_prod_cart_strip_maxi($order_payload, $prod_id, $klass=''){
		if(!$order_payload->qty ){
			return "";
		}
		if(!self::$BASE_URI){
			self::$BASE_URI = "";
		}

		$prod_pix           = self::$BASE_URI . "" . CSRH::get_thumb_equiv($order_payload->pix);
		$data_processor     = self::$BASE_URI . "processors/product_ajax_bridge.php";
		$prod_foto          = "<img class='thumbnail ordered_item_minipix' src='{$prod_pix}' alt='$order_payload->prod_title' style='max-width: 100%' ";
		$prod_foto         .= " data-prod-id='{$prod_id}' ";
		$prod_foto         .= " data-prod-catid='{$order_payload->cat_id}' ";
		$prod_foto         .= " data-prod-attrid='{$order_payload->attrib_id}' ";
		$prod_foto         .= " data-cat-id='{$prod_id}' ";
		$prod_foto         .= " data-tip=\"Anklicken um<br><strong style='color:#900;'>Alle [{$order_payload->qty}] " . "</strong> zu <strong style='color:#900;'>l&ouml;schen</strong>\" ";
		$prod_foto         .= " data-prod-price='{$order_payload->price}' ";
		$prod_foto         .= " data-total_price='{$order_payload->total}' ";
		$prod_foto         .= " data-prod-size='{$order_payload->size}' ";
		$prod_foto         .= " data-color='{$order_payload->color}' ";
		$prod_foto         .= " data-size_unit='{$order_payload->size_unit}' ";
		$prod_foto         .= " data-prod-qty='{$order_payload->qty}' ";
		$prod_foto         .= " data-prod-make='{$order_payload->man_title}' ";
		$prod_foto         .= " data-processor='{$data_processor}' ";
		$prod_foto         .= " data-endpoint='/en/shop/api/v1/products/delete_item_cluster/{$order_payload->attrib_id}/{$order_payload->cat_id}/{$prod_id}/{$order_payload->price}/{$order_payload->qty}' ";
		$prod_foto         .= "/>";

		$prod_cart_strip    = "<div class='col-md-12 pad_minimal {$klass}' >";
		$prod_cart_strip   .= "<div class='col-md-2 no_padding cart_prod_pix' >";
		$prod_cart_strip   .= $prod_foto;
		$prod_cart_strip   .= "</div>";
		$prod_cart_strip   .= "<div class='col-md-6 cart_desc_pane'>";
		$prod_cart_strip   .= "<strong>" . $order_payload->prod_title . "</strong> (<strong>x" . $order_payload->qty . "</strong>)<br />";
		$prod_cart_strip   .= "<strong>" . self::normalize_price_val($order_payload->price) . " :: ( " .  $order_payload->size . " " . $order_payload->size_unit . " )</strong><br />";
		if($col = $order_payload->color){
			if($col == "NONE" || $col == "--"){
				$text_color     = "rgba(115, 12, 0, 1); font-style:italic";
				$color_dot_bg   = "background:" . CSRH::get_rgb_transpose($order_payload->color);
				$prod_col       = $order_payload->color;
			}else if($col == "MIXED" || $col == "RAINBOW") {
				$text_color     = "rgba(115, 12, 0, 1); font-style:italic";
				$color_dot_bg   = CSRH::get_rgb_transpose($order_payload->color);
				$prod_col       = self::colorify_text($order_payload->color);
			}else{
				$text_color = CSRH::get_rgb_transpose($col);
				$color_dot_bg   = "background:" . CSRH::get_rgb_transpose($order_payload->color);
				$prod_col       = $order_payload->color;
			}
			$prod_cart_strip   .= "Color: <strong class='color_txt_str' style='color:" . $text_color .";'>" . $prod_col . "<span class='color_dot' style='" . $color_dot_bg .";'></span></strong><br />";
		}
		if(isset($order_payload->prod_id) ){
			$prod_cart_strip   .= "SKU: <strong class='color_txt_str' >" . CSRH::fetch_sku($order_payload->prod_id) . "</strong><br />";
		}
		$prod_cart_strip   .= "</div>";
		$prod_cart_strip   .= "<div class='col-md-4 cart_total_pane'>";
		$prod_cart_strip   .= "<strong>" . self::normalize_price_val($order_payload->total) . "</strong><br />";
		$prod_cart_strip   .= "</div>";

		$prod_cart_strip   .= "</div>";
		$prod_cart_strip   .= "<div style='clear:both;'></div>";
		return $prod_cart_strip;

	}

	public static function colorify_text($color){
		$colored_text       = "";
		$colors             = array(
			0       => "#FF0000",
			1       => "#FF8000",
			2       => "#80FF00",
			3       => "#0080FF",
			4       => "#8000FF",
			5       => "#FF00FF",
			6       => "#FF0080",
			7       => "#000000",
			8       => "#C25109",
			9       => "#32A925",
			10      => "#B2B2B2",
		);
		$color_arr          = str_split($color);
		foreach($color_arr as $color_char){
			$rand_nr        = mt_rand(0, (count($colors)-1));
			$txt_col        = $colors[$rand_nr];
			$colored_text  .= "<span style='color:" . $txt_col . ";' >" . $color_char . "</span>";
		}
		return $colored_text;
	}

	protected static function get_prod_cart_strip($order_payload, $prod_id, $klass=''){
		if(!$order_payload->qty ){
			return "";
		}
		if(!self::$BASE_URI){
			self::$BASE_URI = "";
		}

		$redirect_url       = "index.php?option=com_cs_shop&view=cart";
		$prod_pix           = "/" . preg_replace('#^\.#', '', CSRH::get_thumb_equiv($order_payload->pix));
		$data_processor     = self::$BASE_URI . "processors/variant-processor.php";
		$order_type         = self::get_order_type($order_payload->prod_id);
		$prod_foto          = "<a href='{$redirect_url}' class='cart_view_link'>";
		$prod_foto         .= "<img class='thumbnail ordered_item_minipix' src='{$prod_pix}' alt='$order_payload->prod_title' style='max-width: 100%' ";
		$prod_foto         .= " data-prod-id='{$prod_id}' ";
		$prod_foto         .= " data-prod-catid='{$order_payload->cat_id}' ";
		$prod_foto         .= " data-prod-attrid='{$order_payload->attrib_id}' ";
		$prod_foto         .= " data-cat-id='{$prod_id}' ";
		$prod_foto         .= " data-id='{$prod_id}' ";
		$prod_foto         .= " data-cid='{$order_payload->cat_id}' ";
		$prod_foto         .= " data-aid='{$order_payload->attrib_id}' ";
		$prod_foto         .= " data-tip=\"Anklicken um<br><strong style='color:#900;'>Alle [ " . trim($order_payload->qty) . " ] " . "</strong> zu <strong style='color:#900;'>l&ouml;schen</strong>\" ";
		$prod_foto         .= " data-unit_price='{$order_payload->price}' ";
		$prod_foto         .= " data-total_price='{$order_payload->total}' ";
		$prod_foto         .= " data-size='{$order_payload->size}' ";
		$prod_foto         .= " data-color='{$order_payload->color}' ";
		$prod_foto         .= " data-size_unit='{$order_payload->size_unit}' ";
		$prod_foto         .= " data-qty='{$order_payload->qty}' ";
		$prod_foto         .= " data-order_type='{$order_type}' ";
		$prod_foto         .= " data-processor='{$data_processor}' ";
		$prod_foto         .= " data-endpoint='/en/shop/api/v1/products/delete_item_cluster/{$order_payload->attrib_id}/{$order_payload->cat_id}/{$prod_id}/{$order_payload->price}/{$order_payload->qty}' ";
		$prod_foto         .= "/>";
		$prod_foto         .= "<a/>";
		$prod_cart_strip    = "<div class='col-md-12 pad_minimal {$klass}' >";

		$prod_cart_strip   .= "<div class='col-md-2 no_padding cart_prod_pix' >";
		$prod_cart_strip   .= $prod_foto;
		$prod_cart_strip   .= "</div>";
		$prod_cart_strip   .= "<div class='col-md-6 cart_desc_pane'>";
		$prod_cart_strip   .= "<strong>" . $order_payload->prod_title . "</strong> (<strong>x" . $order_payload->qty . "</strong>)<br />";
		$prod_cart_strip   .= "<strong>" . self::normalize_price_val($order_payload->price) . " :: ( " .  $order_payload->size . " " . $order_payload->size_unit . " )</strong><br />";
		if($col = $order_payload->color){
			if($col == "NONE" || $col == "--"){
				$text_color     = "rgba(115, 12, 0, 1); font-style:italic";
				$color_dot_bg   = "background:" . CSRH::get_rgb_transpose($order_payload->color);
				$prod_col       = $order_payload->color;
			}else if($col == "MIXED" || $col == "RAINBOW") {
				$text_color     = "rgba(115, 12, 0, 1); font-style:italic";
				$color_dot_bg   = CSRH::get_rgb_transpose($order_payload->color);
				$prod_col       = self::colorify_text($order_payload->color);
			}else{
				$text_color = CSRH::get_rgb_transpose($col);
				$color_dot_bg   = "background:" . CSRH::get_rgb_transpose($order_payload->color);
				$prod_col       = $order_payload->color;
			}
			$prod_cart_strip   .= "Color: <strong class='color_txt_str' style='color:" . $text_color .";'>" . $prod_col . "<span class='color_dot' style='" . $color_dot_bg .";'></span></strong><br />";
		}
		if(isset($order_payload->prod_id) ){
			$prod_cart_strip   .= "SKU: <strong class='color_txt_str' >" . CSRH::fetch_sku($order_payload->prod_id) . "</strong><br />";
		}
		$prod_cart_strip   .= "</div>";
		$prod_cart_strip   .= "<div class='col-md-4 cart_total_pane'>";
		$prod_cart_strip   .= "<strong>" . self::normalize_price_val($order_payload->total) . "</strong><br />";
		$prod_cart_strip   .= "</div>";

		$prod_cart_strip   .= "</div>";
		$prod_cart_strip   .= "<div style='clear:both;'></div>";
		return $prod_cart_strip;
	}

	protected static function get_normlized_order($order_payload, $prod_id, $klass=''){
		$order_obj              = new \stdClass();
		$order_obj->order_date  = time();
		if(!self::$BASE_URI){
			self::$BASE_URI = "";
		}

		if(!@$order_payload->qty ){
			$order_obj->url         = null;
			$order_obj->pix         = null;
			$order_obj->processor   = self::$BASE_URI . "processors/variant-processor.php";
			$order_obj->order_type  = null;
			$order_obj->cid         = null;
			$order_obj->pid         = null;
			$order_obj->aid         = null;
			$order_obj->aid         = null;
			$order_obj->price       = null;
			$order_obj->total       = null;
			$order_obj->qty         = null;
			$order_obj->size_unit   = null;
			$order_obj->size        = null;
			$order_obj->color       = null;
			$order_obj->title       = null;
			$order_obj->endpoint    = null;
			return $order_obj;
		}
		$order_obj->url         = "index.php?option=com_cs_shop&view=cart";
		$order_obj->pix         = str_replace("components/com_cs_shop/mvc/", "", self::$BASE_URI . $order_payload->pix);
		$order_obj->processor   = self::$BASE_URI . "processors/variant-processor.php";
		$order_obj->order_type  = self::get_order_type($order_payload->prod_id);
		$order_obj->cid         = $order_payload->cat_id;
		$order_obj->pid         = $order_payload->prod_id;
		$order_obj->aid         = $order_payload->attrib_id;
		$order_obj->aid         = $order_payload->attrib_id;
		$order_obj->price       = $order_payload->price;
		$order_obj->total       = $order_payload->total;
		$order_obj->qty         = $order_payload->qty;
		$order_obj->size_unit   = $order_payload->size_unit;
		$order_obj->size        = $order_payload->size;
		$order_obj->color       = $order_payload->color;
		$order_obj->title       = $order_payload->prod_title;
		$order_obj->endpoint    = "/en/shop/api/v1/products/delete_item_cluster/{$order_payload->attrib_id}/{$order_payload->cat_id}/{$prod_id}/{$order_payload->price}/{$order_payload->qty}";
		return $order_obj;
	}

	public static function normalize_price_val($price, $cur=null){
		$cur    = $cur? $cur : CSCurrency::get_active_currency();
		$price  = $cur .  " " . number_format($price, 2, ".", "'");
		return $price;
	}

	public static function set_filterable_path($filter_path){
		if(!in_array($filter_path, self::$filters)){
			self::$filters[]    = $filter_path;
		}
	}

	public static function set_base_uri($base_uri){
		self::$BASE_URI = $base_uri;
	}

}