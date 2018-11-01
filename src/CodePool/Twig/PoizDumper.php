<?php

	namespace App\CodePool\Twig;

	use App\CodePool\Helpers\Builders\InterFaces\PoizProductInterface;
	use App\CodePool\ProductHelpers\CSOrderManager;
	use Carbon\Carbon;
	use Doctrine\DBAL\Connection;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

	class PoizDumper extends \Twig_Extension {


		/**
		 * @var \Twig_Environment
		 */
		protected $twigEnv;

		/**
		 * @var UrlGeneratorInterface
		 */
		protected $urlGenerator;
		
		/**
		 * @var Carbon
		 */
		protected $carbon;
		/**
		 * @var EntityManagerInterface
		 */
		protected $em;
		
		/**
		 * @var Connection
		 */
		protected $conn;

		public function __construct(\Twig_Environment $twigEnv, EntityManagerInterface $em){
			$this->em           = $em;
			$this->conn         = $this->em->getConnection();
			$this->twigEnv      = $twigEnv;
		}

		/**
		 * @return array|\Twig_Filter[]
		 */
		public function getFilters() {
			return [
				new \Twig_SimpleFilter('stripATSymbol', [$this, 'stripATSymbolFromEmail'], ['is_safe'=>['html']]),
				new \Twig_SimpleFilter('dateDiff4Humans', [$this, 'dateDiff4Humans'], ['is_safe'=>['html']]),
				new \Twig_SimpleFilter('formatPrice', [$this, 'formatPrice'], ['is_safe'=>['html']]),
			];
		}

		/**
		 * @return array|\Twig_Function[]
		 */
		public function getFunctions() {
			return [
				new \Twig_SimpleFunction('poiz_dump', [$this, 'dumpVars'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('p_dump', [$this, 'dumpVars'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('pDump', [$this, 'dumpVars'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('poizDump', [$this, 'dumpVars'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('pzDump', [$this, 'dumpVars'], ['is_safe'=>['html'], 'needs_context' => true, 'needs_environment' => true]),
				new \Twig_SimpleFunction('buildRoute', [$this, 'buildRoute'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('overRideArrayVal', [$this, 'overRideArrayVal'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('exec', [$this, 'execClosure'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('execClosure', [$this, 'execClosure'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('php', [$this, 'executeArbitraryPHPCode'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('runSQL', [$this, 'executeArbitrarySQLAgainstActiveDB'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('sql', [$this, 'executeArbitrarySQLAgainstActiveDB'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('getVariantsData', [$this, 'getVariantsData'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('getOrderTotalAndQuantity', [$this, 'getOrderTotalAndQuantityFromSession'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('pzConcat', [$this, 'pzConcat'], ['is_safe'=>['html']]),
				new \Twig_SimpleFunction('getMenuItems', [$this, 'getMenuItems'], ['is_safe'=>['html']]),
			];
		}

		public function useFilterNr1($str){
			return strtolower($str);
		}

		public function execClosure($closure, ...$data){
			$output = null;
			if($data) {
				try {
					$output = call_user_func_array($closure, $data);
				} catch ( \Exception $e ) {
					$output = $e->getMessage();
				}
			}else{
				$output = call_user_func_array($closure, []);
			}
			if ( is_array( $output ) || is_object( $output ) ) {
				$output = var_export( $output, true );
			}
			return $output;
		}

		public function executeArbitraryPHPCode($phpCodeAsString='var_dump(2)'){
			return eval($phpCodeAsString);
		}

		public function stripATSymbolFromEmail($strEmail){
			return ucfirst( preg_replace("#@.+$#", "", $strEmail));
		}
		
		public function dumpVars(\Twig_Environment $env, $context, ...$vars){  #public function dumpVars($data){
			if (!$this->twigEnv->isDebug()) {
				return;
			}

			ob_start();

			if (!$vars) {
				$vars = array();
				foreach ($context as $key => $value) {
					if (!$value instanceof \Twig_Template) {
						$vars[$key] = $value;
					}
				}

				dump($vars);
			} else {
				dump(...$vars);
			}
			return ob_get_clean();
		}

		public function buildRoute($routeName, $params=['_locale'=>'en']){
			/** @var UrlGeneratorInterface $urlGenerator */
			return $this->urlGenerator->generate($routeName, $params);
		}

		public function overRideArrayVal($array, $keyValPairs=[]){
			foreach($keyValPairs as $key=>$val){
				if(array_key_exists($key, $array)){
					$array[$key]    = $val;
				}
			}
			return $array;
		}
		
		public function dateDiff4Humans($strDateOrDateTimeObj){
			$strDate    = $strDateOrDateTimeObj;
			if($strDateOrDateTimeObj instanceof \DateTime){
				$strDate= $strDateOrDateTimeObj->format("Y-m-d H:i:s");
			}
			$date       = $this->carbon::parse($strDate);   //->locale('de_CH');
			return $date->diffForHumans();
		}
		
		public function pzConcat(...$rest){
			return implode("", $rest);
		}
		
		/**
		 * @param       $sql
		 * @param array $socRayParams   ASSOCIATIVE ARRAY OF KEY-VALUE PAIRS TO BE FED INTO THE PREPARED SQL
		 * @param int   $returnType     1 => ARRAY OF OBJECTS, 2 => ARRAY OF ASSOCIATIVE ARRAYS, 3 => SINGLE OBJECT, 4 => SINGLE ASSOC. ARRAY, 5 => SINGLE VALUE AS STRING, 6 => SINGLE VALUE AS INT
		 *
		 * @throws \Doctrine\DBAL\DBALException
		 * @return mixed
		 */
		public function executeArbitrarySQLAgainstActiveDB($sql, $socRayParams=[], $returnType=1){
			$dataMap    = [
				1 => \PDO::FETCH_OBJ,
				2 => \PDO::FETCH_ASSOC,
				3 => \PDO::FETCH_OBJ,
				4 => \PDO::FETCH_ASSOC,
				5 => \PDO::FETCH_COLUMN,
				6 => \PDO::FETCH_NUM,
				
			];
			$statement  = $this->conn->prepare($sql);
			$statement->execute($socRayParams);
			if(in_array($returnType, [1, 2])){
				$result = $statement->fetchAll($dataMap[$returnType]);
			}elseif (in_array($returnType, [3, 4, 6])){
				$result = $statement->fetch($dataMap[$returnType]);
			}elseif ($returnType == 5){
				$result = $statement->fetchColumn();
			}else{
				$result = $statement->fetchAll($dataMap[$returnType]);
			}
			return $result;
		}
		
		public function formatPrice($price){
			return number_format($price, 2, '.', "'");
		}
		
		public function getOrderTotalAndQuantityFromSession(PoizProductInterface $product) {
			$contextPayload = CSOrderManager::get_context_payload($product->getProductCID(), $product->getProductID(), $product->getProductAID());
			$qty            = $product->getOrderQuantity() . "0";
			$qty            = isset($contextPayload->qty) && $contextPayload->qty ? $contextPayload->qty : $qty;
			$total          = isset($contextPayload->cxt_total) && $contextPayload->cxt_total ? number_format($contextPayload->cxt_total, 2, '.', "'") : '0.00';
			return [
				'total'     => $total,
				'quantity'  => $qty,
			];
		}
		
		public function getVariantsData(PoizProductInterface $product, $followLink=false) {
			$separator      = ' | ';
			$variantImages  = ($tmp = $product->getVariantImages())      ? explode($separator, $tmp): null;
			$variantColors  = ($tmp = $product->getVariantColors())      ? explode($separator, $tmp) : null;
			$variantPrices  = ($tmp = $product->getVariantPrices())      ? explode($separator, $tmp) : null;
			$variantSPrices = ($tmp = $product->getVariantSalePrices())  ? explode($separator, $tmp) : null;
			$VariantSStates = ($tmp = $product->getVariantSaleStates())  ? explode($separator, $tmp) : null;
			$variantAIDS    = ($tmp = $product->getVariantAIDS())        ? explode($separator, $tmp) : null;
			$variantData    = [];
			if(sizeof($variantAIDS) > 1){
				foreach($variantAIDS as $iKey=>$variantAID){
					#if($product->getProductAID() == $variantAID){ continue; }
					$rgbColor       =  $product::getRGBTranspose($variantColors[$iKey]);
					$variantPrice   =  ($VariantSStates[$iKey])? $variantSPrices[$iKey] : $variantPrices[$iKey];
					$variantPrice   =  number_format($variantPrice, 2, '.', "'");
					$imgWrapTag     = $followLink ? 'a' : 'div';
					$tempData       = [
						'pix'       => $variantImages[$iKey],
						'color'     => $rgbColor,
						'price'     => $variantPrice,
						'wrapTag'   => $imgWrapTag,
						'aid'       => $variantAID,
						'cid'       => $product->getProductCID(),
						'pid'       => $product->getProductID(),
						'action'    => "getProductVariantData",
						'processor' => "/processors/variant-processor.php",
					];
					$variantData[]  = $tempData;
				}
			}
			return $variantData;
		}
		
		public function getMenuItems() {
			return $this->em->getRepository('App\Entity\PoizBasketCategories')->findAll();
		}

	}
