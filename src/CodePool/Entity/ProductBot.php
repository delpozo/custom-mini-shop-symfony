<?php
	/**
	 * Created by PhpStorm.
	 * User: Poiz Campbell
	 */
	
	namespace App\CodePool\Entity;
	
	
	use App\CodePool\Helpers\Builders\InterFaces\PoizProductInterface;
	use App\CodePool\ProductHelpers\CSCurrency;
	use Doctrine\DBAL\Driver\Connection;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\QueryBuilder;
	use Pimple\Container;
	
	class ProductBot implements PoizProductInterface{
		protected $productID;
		protected $productCID;
		protected $productTitle;
		protected $productAlias;
		protected $productDescription;
		protected $categoryName;
		protected $categoryAlias;
		protected $categoryIcon;
		protected $categoryPrefix;
		protected $productAID;
		protected $sku;
		protected $color;
		protected $size_unit;
		protected $manufacturerID;
		protected $size;
		protected $year;
		protected $weight;
		protected $milage;
		protected $ratings;
		protected $normalPrice;
		protected $salePrice;
		protected $imageHash;
		protected $imageAlias;
		protected $productPix;
		protected $manufacturerName;
		protected $manufacturerLogo;
		protected $manufacturerDescription;
		protected $variantImages;
		protected $variantPrices;
		protected $variantSalePrices;
		protected $variantSaleStates;
		protected $variantSizes;
		protected $variantWeights;
		protected $variantColors;
		protected $variantSizeUnits;
		protected $variantMilages;
		protected $variantRatings;
		protected $variantYears;
		protected $variantAIDS;
		protected $processor;
		protected $restApiBase;
		protected $preLoaderIcon;
		protected $addIconURL;
		protected $deleteIconURL;
		protected $reviewIconURL;
		protected $tweeterIconURL;
		protected $facebookIconURL;
		protected $recommendIconURL;
		protected $favoritesIconURL;
		protected $checkoutIconURL;
		protected $productThumbnail;
		protected $facebookLinkURL;
		protected $twitterLinkURL;
		protected $instagramLinkURL;
		protected $linkedinLinkURL;
		protected $xingLinkURL;
		protected $recommendLinkURL;
		protected $googlePlusLinkURL;
		protected $diggLinkURL;
		protected $dataAddAction;
		protected $dataDelAction;
		protected $productGlobeBox;
		protected $activeCurrency;
		protected $QTotalPrice;
		protected $orderQuantity;
		protected $orderTotalValue;
		protected $onSale;
		
		private $assetsStoreURI = "http://127.0.0.1/joy.pz/public";
		/**
		 * @var \Doctrine\ORM\EntityManager
		 */
		private $em;
		/**
		 * @var \Doctrine\ORM\QueryBuilder
		 */
		private $dql;
		/**
		 * @var \Doctrine\DBAL\Driver\Connection
		 */
		private $connection;
		/**
		 * @var \Pimple\Container
		 */
		private $container;
		
		#private $assetsStoreURI = "http://127.0.0.1/soundspace/public";
		
		
		
		/**
		 * SoundSpace constructor.
		 *
		 * @param \Doctrine\ORM\EntityManager      $em
		 * @param \Doctrine\ORM\QueryBuilder       $dql
		 * @param \Doctrine\DBAL\Driver\Connection $connection
		 * @param \Pimple\Container                $container
		 */
		public function __construct(EntityManager $em, QueryBuilder $dql, Connection $connection, Container $container) {
			$this->em           = $em;
			$this->dql          = $dql;
			$this->connection   = $connection;
			$this->container    = $container;
		}
		
		public function getSingleItemSQL($aid, $cid, $pid){
		
		}
		
		public function getSQL($dbDriver='pdo_mysql', $categoryID=1, $aid=null, $pid=null){
			$SEPARATOR      = $dbDriver == 'pdo_sqlite' ? ',' : 'SEPARATOR';
			$config         = self::getSetUpConfig();
			$sql            =<<<SQL
SELECT
PROD.id             AS productID,
PROD.cat_id         AS productCID,
PROD.title          AS productTitle,
PROD.alias          AS productAlias,
PROD.description    AS productDescription,

CAT.title           AS categoryName,
CAT.alias           AS categoryAlias,
CAT.icon            AS categoryIcon,
CAT.prefix          AS categoryPrefix,

ATR.id              AS productAID,
ATR.sku             AS sku,
ATR.color           AS color,
ATR.size_unit       AS size_unit,
ATR.manufacturer    AS manufacturerID,
ATR.size            AS size,
ATR.year            AS year,
ATR.weight          AS weight,
ATR.milage          AS milage,
ATR.ratings         AS ratings,

PRX.normal_price    AS normalPrice,
PRX.discount_price  AS salePrice,
PRX.on_sale  		AS onSale,

IMG.hash            AS imageHash,
IMG.alias           AS imageAlias,
IMG.pix             AS productPix,

MAN.title           AS manufacturerName,
MAN.logo            AS manufacturerLogo,
MAN.description     AS manufacturerDescription,

(SELECT GROUP_CONCAT(FT.pix {$SEPARATOR}               ' | ') FROM  `poiz_basket_images`       AS FT   JOIN `poiz_basket_attributes` 	AS BUT ON BUT.id=FT.attrib_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)	AS variantImages,
(SELECT GROUP_CONCAT(MNY.price {$SEPARATOR}            ' | ') FROM  `poiz_basket_prices`       AS MNY  JOIN `poiz_basket_attributes` 	AS BUT ON BUT.id=MNY.attrib_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantPrices,
(SELECT GROUP_CONCAT(MNY.discount_price {$SEPARATOR}   ' | ') FROM  `poiz_basket_prices`       AS MNY	JOIN `poiz_basket_attributes` 	AS BUT ON BUT.id=MNY.attrib_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantSalePrices,
(SELECT GROUP_CONCAT(MNY.on_sale {$SEPARATOR}   		' | ') FROM  `poiz_basket_prices`       AS MNY	JOIN `poiz_basket_attributes` 	AS BUT ON BUT.id=MNY.attrib_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantSaleStates,
(SELECT GROUP_CONCAT(BUT.size {$SEPARATOR}            	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantSizes,
(SELECT GROUP_CONCAT(BUT.id {$SEPARATOR}            	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantAIDS,
(SELECT GROUP_CONCAT(BUT.weight {$SEPARATOR}          	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantWeights,
(SELECT GROUP_CONCAT(BUT.color {$SEPARATOR}           	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantColors,
(SELECT GROUP_CONCAT(BUT.size_unit {$SEPARATOR}       	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantSizeUnits,
(SELECT GROUP_CONCAT(BUT.milage {$SEPARATOR}          	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantMilages,
(SELECT GROUP_CONCAT(BUT.ratings {$SEPARATOR}         	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products` 	AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantRatings,
(SELECT GROUP_CONCAT(BUT.year {$SEPARATOR}            	' | ') FROM  `poiz_basket_attributes`   AS BUT 	JOIN `poiz_basket_products`		AS PDT ON PDT.id=BUT.prod_id	WHERE BUT.prod_id=PROD.id AND BUT.published=1)  AS variantYears


SQL;
			
			if($config){
				$addenda    = ',' . PHP_EOL;
				foreach($config as $key=>$value){
					$addenda .= " ( SELECT '" . $value  . "' )\t\tAS\t" . $key . ",\n";
				}
				$sql    .=  rtrim(trim($addenda), ',') . " ";
			}
			
			$sql           .=<<<SQL
FROM `poiz_basket_products`             AS PROD
LEFT JOIN `poiz_basket_categories`      AS  CAT ON PROD.cat_id=CAT.id
LEFT JOIN `poiz_basket_attributes`      AS  ATR ON PROD.id=ATR.prod_id
LEFT JOIN `poiz_basket_images`          AS  IMG ON PROD.id=IMG.prod_id AND IMG.attrib_id=ATR.id
LEFT JOIN `poiz_basket_prices`          AS  PRX ON PROD.id=PRX.prod_id AND PRX.attrib_id=ATR.id
LEFT JOIN `poiz_basket_manufacturers`   AS  MAN ON ATR.manufacturer=MAN.id

WHERE CAT.published=1
AND PROD.published=1
AND ATR.published=1
SQL;
			
			
			
			$sql .= $categoryID ? " AND  PROD.cat_id={$categoryID} "   . PHP_EOL : "";
			$sql .= $categoryID ? " AND  CAT.id={$categoryID} "         . PHP_EOL : "";
			$sql .= $aid ? " AND  ATR.id={$aid} "   . PHP_EOL : "";
			$sql .= $pid ? " AND PROD.id={$pid} "   . PHP_EOL : "";
			$sql .=" GROUP BY PROD.id " . PHP_EOL;
			$sql .=" ORDER BY PROD.title " . PHP_EOL;

			
			return $sql;
		}
		
		private static function getSetUpConfig($iconsBase = "http://react.poiz.pz/api-data/images/icons/"){
			return [
				'processor'         => 'http://react.poiz.pz/processors/',
				'activeCurrency'    => CSCurrency::get_active_currency(),
				'restApiBase'       => 'http://react.poiz.pz/api/',
				'preLoaderIcon'     => $iconsBase . '/preloader_transparent.gif',
				'addIconURL'        => $iconsBase . 'add_product_icon.png',
				'deleteIconURL'     => $iconsBase . 'delete_product_icon.png',
				
				'reviewIconURL'     => $iconsBase . '_32_by_32/reviews.png',
				'tweeterIconURL'    => $iconsBase . '_32_by_32/tweeter.png',
				'facebookIconURL'   => $iconsBase . '_32_by_32/facebook_share.png',
				'recommendIconURL'  => $iconsBase . '_32_by_32/recommend.png',
				'favoritesIconURL'  => $iconsBase . '_32_by_32/favoritez.png',
				'checkoutIconURL'   => $iconsBase . '_32_by_32/checkout.png',
				'productThumbnail'  => $iconsBase . 'add_product_icon.png',
				
				'facebookLinkURL'   => 'https://facebook.com/',
				'twitterLinkURL'    => 'https://twitter.com/',
				'instagramLinkURL'  => 'https://instagram.com/',
				'linkedinLinkURL'   => 'https://linkedin.com/',
				'xingLinkURL'       => 'https://xing.com/',
				'recommendLinkURL'  => 'https://googleplus.com/',
				'googlePlusLinkURL' => 'https://googleplus.com/',
				'diggLinkURL'       => 'https://digg.com/',
				
				'dataAddAction'     => 'add_items_to_cart',
				'dataDelAction'     => 'delete_items_from_cart',
				'productGlobeBox'   => 'prod_data_box',
			];
		}
		
		/**
		 * @return mixed
		 */
		public function getProductID() {
			return $this->productID;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductCID() {
			return $this->productCID;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductTitle() {
			return $this->productTitle;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductAlias() {
			return $this->productAlias;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductDescription() {
			return $this->productDescription;
		}
		
		/**
		 * @return mixed
		 */
		public function getCategoryName() {
			return $this->categoryName;
		}
		
		/**
		 * @return mixed
		 */
		public function getCategoryAlias() {
			return $this->categoryAlias;
		}
		
		/**
		 * @return mixed
		 */
		public function getCategoryIcon() {
			return $this->categoryIcon;
		}
		
		/**
		 * @return mixed
		 */
		public function getCategoryPrefix() {
			return $this->categoryPrefix;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductAID() {
			return $this->productAID;
		}
		
		/**
		 * @return mixed
		 */
		public function getSku() {
			return $this->sku;
		}
		
		/**
		 * @return mixed
		 */
		public function getColor() {
			return $this->color;
		}
		
		/**
		 * @return mixed
		 */
		public function getSizeUnit() {
			return $this->size_unit;
		}
		
		/**
		 * @return mixed
		 */
		public function getManufacturerID() {
			return $this->manufacturerID;
		}
		
		/**
		 * @return mixed
		 */
		public function getSize() {
			return $this->size;
		}
		
		/**
		 * @return mixed
		 */
		public function getYear() {
			return $this->year;
		}
		
		/**
		 * @return mixed
		 */
		public function getWeight() {
			return $this->weight;
		}
		
		/**
		 * @return mixed
		 */
		public function getMilage() {
			return $this->milage;
		}
		
		/**
		 * @return mixed
		 */
		public function getRatings() {
			return $this->ratings;
		}
		
		/**
		 * @return mixed
		 */
		public function getNormalPrice() {
			return $this->normalPrice;
		}
		
		/**
		 * @return mixed
		 */
		public function getSalePrice() {
			return $this->salePrice;
		}
		
		/**
		 * @return mixed
		 */
		public function getImageHash() {
			return $this->imageHash;
		}
		
		/**
		 * @return mixed
		 */
		public function getImageAlias() {
			return $this->imageAlias;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductPix() {
			return $this->productPix;
		}
		
		/**
		 * @return mixed
		 */
		public function getManufacturerName() {
			return $this->manufacturerName;
		}
		
		/**
		 * @return mixed
		 */
		public function getManufacturerLogo() {
			return $this->manufacturerLogo;
		}
		
		/**
		 * @return mixed
		 */
		public function getManufacturerDescription() {
			return $this->manufacturerDescription;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantAIDS() {
			return $this->variantAIDS;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantImages() {
			return $this->variantImages;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantPrices() {
			return $this->variantPrices;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantSalePrices() {
			return $this->variantSalePrices;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantSizes() {
			return $this->variantSizes;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantWeights() {
			return $this->variantWeights;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantColors() {
			return $this->variantColors;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantSizeUnits() {
			return $this->variantSizeUnits;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantMilages() {
			return $this->variantMilages;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantRatings() {
			return $this->variantRatings;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantYears() {
			return $this->variantYears;
		}
		
		/**
		 * @return mixed
		 */
		public function getProcessor() {
			return $this->processor;
		}
		
		/**
		 * @return mixed
		 */
		public function getRestApiBase() {
			return $this->restApiBase;
		}
		
		/**
		 * @return mixed
		 */
		public function getPreLoaderIcon() {
			return $this->preLoaderIcon;
		}
		
		/**
		 * @return mixed
		 */
		public function getAddIconURL() {
			return $this->addIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getDeleteIconURL() {
			return $this->deleteIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getReviewIconURL() {
			return $this->reviewIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getTweeterIconURL() {
			return $this->tweeterIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getFacebookIconURL() {
			return $this->facebookIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getRecommendIconURL() {
			return $this->recommendIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getFavoritesIconURL() {
			return $this->favoritesIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getCheckoutIconURL() {
			return $this->checkoutIconURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductThumbnail() {
			return $this->productThumbnail;
		}
		
		/**
		 * @return mixed
		 */
		public function getFacebookLinkURL() {
			return $this->facebookLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getTwitterLinkURL() {
			return $this->twitterLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getInstagramLinkURL() {
			return $this->instagramLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getLinkedinLinkURL() {
			return $this->linkedinLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getXingLinkURL() {
			return $this->xingLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getRecommendLinkURL() {
			return $this->recommendLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getGooglePlusLinkURL() {
			return $this->googlePlusLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getDiggLinkURL() {
			return $this->diggLinkURL;
		}
		
		/**
		 * @return mixed
		 */
		public function getDataAddAction() {
			return $this->dataAddAction;
		}
		
		/**
		 * @return mixed
		 */
		public function getDataDelAction() {
			return $this->dataDelAction;
		}
		
		/**
		 * @return mixed
		 */
		public function getProductGlobeBox() {
			return $this->productGlobeBox;
		}
		
		/**
		 * @return mixed
		 */
		public function getActiveCurrency() {
			return $this->activeCurrency;
		}
		
		/**
		 * @return mixed
		 */
		public function getQTotalPrice() {
			return $this->QTotalPrice;
		}
		
		/**
		 * @return mixed
		 */
		public function getOrderQuantity() {
			return $this->orderQuantity;
		}
		
		/**
		 * @return mixed
		 */
		public function getOrderTotalValue() {
			return $this->orderTotalValue;
		}
		
		/**
		 * @return mixed
		 */
		public function getOnSale() {
			return $this->onSale;
		}
		
		/**
		 * @return mixed
		 */
		public function getVariantSaleStates() {
			return $this->variantSaleStates;
		}
		
		
		
		
		/**
		 * @param mixed $productID
		 */
		public function setProductID( $productID ): void {
			$this->productID = $productID;
		}
		
		/**
		 * @param mixed $productCID
		 */
		public function setProductCID( $productCID ): void {
			$this->productCID = $productCID;
		}
		
		/**
		 * @param mixed $productTitle
		 */
		public function setProductTitle( $productTitle ): void {
			$this->productTitle = $productTitle;
		}
		
		/**
		 * @param mixed $productAlias
		 */
		public function setProductAlias( $productAlias ): void {
			$this->productAlias = $productAlias;
		}
		
		/**
		 * @param mixed $productDescription
		 */
		public function setProductDescription( $productDescription ): void {
			$this->productDescription = $productDescription;
		}
		
		/**
		 * @param mixed $categoryName
		 */
		public function setCategoryName( $categoryName ): void {
			$this->categoryName = $categoryName;
		}
		
		/**
		 * @param mixed $categoryAlias
		 */
		public function setCategoryAlias( $categoryAlias ): void {
			$this->categoryAlias = $categoryAlias;
		}
		
		/**
		 * @param mixed $categoryIcon
		 */
		public function setCategoryIcon( $categoryIcon ): void {
			$this->categoryIcon = $categoryIcon;
		}
		
		/**
		 * @param mixed $categoryPrefix
		 */
		public function setCategoryPrefix( $categoryPrefix ): void {
			$this->categoryPrefix = $categoryPrefix;
		}
		
		/**
		 * @param mixed $productAID
		 */
		public function setProductAID( $productAID ): void {
			$this->productAID = $productAID;
		}
		
		/**
		 * @param mixed $sku
		 */
		public function setSku( $sku ): void {
			$this->sku = $sku;
		}
		
		/**
		 * @param mixed $color
		 */
		public function setColor( $color ): void {
			$this->color = $color;
		}
		
		/**
		 * @param mixed $size_unit
		 */
		public function setSizeUnit( $size_unit ): void {
			$this->size_unit = $size_unit;
		}
		
		/**
		 * @param mixed $manufacturerID
		 */
		public function setManufacturerID( $manufacturerID ): void {
			$this->manufacturerID = $manufacturerID;
		}
		
		/**
		 * @param mixed $size
		 */
		public function setSize( $size ): void {
			$this->size = $size;
		}
		
		/**
		 * @param mixed $year
		 */
		public function setYear( $year ): void {
			$this->year = $year;
		}
		
		/**
		 * @param mixed $weight
		 */
		public function setWeight( $weight ): void {
			$this->weight = $weight;
		}
		
		/**
		 * @param mixed $milage
		 */
		public function setMilage( $milage ): void {
			$this->milage = $milage;
		}
		
		/**
		 * @param mixed $ratings
		 */
		public function setRatings( $ratings ): void {
			$this->ratings = $ratings;
		}
		
		/**
		 * @param mixed $normalPrice
		 */
		public function setNormalPrice( $normalPrice ): void {
			$this->normalPrice = $normalPrice;
		}
		
		/**
		 * @param mixed $salePrice
		 */
		public function setSalePrice( $salePrice ): void {
			$this->salePrice = $salePrice;
		}
		
		/**
		 * @param mixed $imageHash
		 */
		public function setImageHash( $imageHash ): void {
			$this->imageHash = $imageHash;
		}
		
		/**
		 * @param mixed $imageAlias
		 */
		public function setImageAlias( $imageAlias ): void {
			$this->imageAlias = $imageAlias;
		}
		
		/**
		 * @param mixed $productPix
		 */
		public function setProductPix( $productPix ): void {
			$this->productPix = $productPix;
		}
		
		/**
		 * @param mixed $manufacturerName
		 */
		public function setManufacturerName( $manufacturerName ): void {
			$this->manufacturerName = $manufacturerName;
		}
		
		/**
		 * @param mixed $manufacturerLogo
		 */
		public function setManufacturerLogo( $manufacturerLogo ): void {
			$this->manufacturerLogo = $manufacturerLogo;
		}
		
		/**
		 * @param mixed $manufacturerDescription
		 */
		public function setManufacturerDescription( $manufacturerDescription ): void {
			$this->manufacturerDescription = $manufacturerDescription;
		}
		
		/**
		 * @param mixed $variantAIDS
		 */
		public function setVariantAIDS( $variantAIDS ): void {
			$this->variantAIDS = $variantAIDS;
		}
		
		/**
		 * @param mixed $variantImages
		 */
		public function setVariantImages( $variantImages ): void {
			$this->variantImages = $variantImages;
		}
		
		/**
		 * @param mixed $variantPrices
		 */
		public function setVariantPrices( $variantPrices ): void {
			$this->variantPrices = $variantPrices;
		}
		
		/**
		 * @param mixed $variantSalePrices
		 */
		public function setVariantSalePrices( $variantSalePrices ): void {
			$this->variantSalePrices = $variantSalePrices;
		}
		
		/**
		 * @param mixed $variantSizes
		 */
		public function setVariantSizes( $variantSizes ): void {
			$this->variantSizes = $variantSizes;
		}
		
		/**
		 * @param mixed $variantWeights
		 */
		public function setVariantWeights( $variantWeights ): void {
			$this->variantWeights = $variantWeights;
		}
		
		/**
		 * @param mixed $variantColors
		 */
		public function setVariantColors( $variantColors ): void {
			$this->variantColors = $variantColors;
		}
		
		/**
		 * @param mixed $variantSizeUnits
		 */
		public function setVariantSizeUnits( $variantSizeUnits ): void {
			$this->variantSizeUnits = $variantSizeUnits;
		}
		
		/**
		 * @param mixed $variantMilages
		 */
		public function setVariantMilages( $variantMilages ): void {
			$this->variantMilages = $variantMilages;
		}
		
		/**
		 * @param mixed $variantRatings
		 */
		public function setVariantRatings( $variantRatings ): void {
			$this->variantRatings = $variantRatings;
		}
		
		/**
		 * @param mixed $variantYears
		 */
		public function setVariantYears( $variantYears ): void {
			$this->variantYears = $variantYears;
		}
		
		/**
		 * @param mixed $processor
		 */
		public function setProcessor( $processor ): void {
			$this->processor = $processor;
		}
		
		/**
		 * @param mixed $restApiBase
		 */
		public function setRestApiBase( $restApiBase ): void {
			$this->restApiBase = $restApiBase;
		}
		
		/**
		 * @param mixed $preLoaderIcon
		 */
		public function setPreLoaderIcon( $preLoaderIcon ): void {
			$this->preLoaderIcon = $preLoaderIcon;
		}
		
		/**
		 * @param mixed $addIconURL
		 */
		public function setAddIconURL( $addIconURL ): void {
			$this->addIconURL = $addIconURL;
		}
		
		/**
		 * @param mixed $deleteIconURL
		 */
		public function setDeleteIconURL( $deleteIconURL ): void {
			$this->deleteIconURL = $deleteIconURL;
		}
		
		/**
		 * @param mixed $reviewIconURL
		 */
		public function setReviewIconURL( $reviewIconURL ): void {
			$this->reviewIconURL = $reviewIconURL;
		}
		
		/**
		 * @param mixed $tweeterIconURL
		 */
		public function setTweeterIconURL( $tweeterIconURL ): void {
			$this->tweeterIconURL = $tweeterIconURL;
		}
		
		/**
		 * @param mixed $facebookIconURL
		 */
		public function setFacebookIconURL( $facebookIconURL ): void {
			$this->facebookIconURL = $facebookIconURL;
		}
		
		/**
		 * @param mixed $recommendIconURL
		 */
		public function setRecommendIconURL( $recommendIconURL ): void {
			$this->recommendIconURL = $recommendIconURL;
		}
		
		/**
		 * @param mixed $favoritesIconURL
		 */
		public function setFavoritesIconURL( $favoritesIconURL ): void {
			$this->favoritesIconURL = $favoritesIconURL;
		}
		
		/**
		 * @param mixed $checkoutIconURL
		 */
		public function setCheckoutIconURL( $checkoutIconURL ): void {
			$this->checkoutIconURL = $checkoutIconURL;
		}
		
		/**
		 * @param mixed $productThumbnail
		 */
		public function setProductThumbnail( $productThumbnail ): void {
			$this->productThumbnail = $productThumbnail;
		}
		
		/**
		 * @param mixed $facebookLinkURL
		 */
		public function setFacebookLinkURL( $facebookLinkURL ): void {
			$this->facebookLinkURL = $facebookLinkURL;
		}
		
		/**
		 * @param mixed $twitterLinkURL
		 */
		public function setTwitterLinkURL( $twitterLinkURL ): void {
			$this->twitterLinkURL = $twitterLinkURL;
		}
		
		/**
		 * @param mixed $instagramLinkURL
		 */
		public function setInstagramLinkURL( $instagramLinkURL ): void {
			$this->instagramLinkURL = $instagramLinkURL;
		}
		
		/**
		 * @param mixed $linkedinLinkURL
		 */
		public function setLinkedinLinkURL( $linkedinLinkURL ): void {
			$this->linkedinLinkURL = $linkedinLinkURL;
		}
		
		/**
		 * @param mixed $xingLinkURL
		 */
		public function setXingLinkURL( $xingLinkURL ): void {
			$this->xingLinkURL = $xingLinkURL;
		}
		
		/**
		 * @param mixed $recommendLinkURL
		 */
		public function setRecommendLinkURL( $recommendLinkURL ): void {
			$this->recommendLinkURL = $recommendLinkURL;
		}
		
		/**
		 * @param mixed $googlePlusLinkURL
		 */
		public function setGooglePlusLinkURL( $googlePlusLinkURL ): void {
			$this->googlePlusLinkURL = $googlePlusLinkURL;
		}
		
		/**
		 * @param mixed $diggLinkURL
		 */
		public function setDiggLinkURL( $diggLinkURL ): void {
			$this->diggLinkURL = $diggLinkURL;
		}
		
		/**
		 * @param mixed $dataAddAction
		 */
		public function setDataAddAction( $dataAddAction ): void {
			$this->dataAddAction = $dataAddAction;
		}
		
		/**
		 * @param mixed $dataDelAction
		 */
		public function setDataDelAction( $dataDelAction ): void {
			$this->dataDelAction = $dataDelAction;
		}
		
		/**
		 * @param mixed $productGlobeBox
		 */
		public function setProductGlobeBox( $productGlobeBox ): void {
			$this->productGlobeBox = $productGlobeBox;
		}
		
		/**
		 * @param mixed $activeCurrency
		 */
		public function setActiveCurrency( $activeCurrency ): void {
			$this->activeCurrency = $activeCurrency;
		}
		
		/**
		 * @param mixed $QTotalPrice
		 */
		public function setQTotalPrice( $QTotalPrice ): void {
			$this->QTotalPrice = $QTotalPrice;
		}
		
		/**
		 * @param mixed $orderQuantity
		 */
		public function setOrderQuantity( $orderQuantity ): void {
			$this->orderQuantity = $orderQuantity;
		}
		
		/**
		 * @param mixed $orderTotalValue
		 */
		public function setOrderTotalValue( $orderTotalValue ): void {
			$this->orderTotalValue = $orderTotalValue;
		}
		
		/**
		 * @param mixed $onSale
		 */
		public function setOnSale( $onSale ): void {
			$this->onSale = $onSale;
		}
		
		/**
		 * @param mixed $variantSaleStates
		 */
		public function setVariantSaleStates( $variantSaleStates ): void {
			$this->variantSaleStates = $variantSaleStates;
		}
		
		
		public static function getRGBTranspose($color){
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
			
			if( isset ($colors_ar[strtoupper($color)]) ){
				return $colors_ar[strtoupper($color)];
			}
			return $colors_ar["NONE"];
		}
	}
	