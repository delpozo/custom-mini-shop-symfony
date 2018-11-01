<?php
	/**
	 * Created by PhpStorm.
	 * User: Poiz Campbell
	 */
	
	namespace App\Controller;
	
	require_once __DIR__ . "/../CodePool/_DEFINITIONS_.php";
	
	use App\CodePool\Entity\ProductBotProxy;
	use App\CodePool\ProductHelpers\CSCartHelper;
	use App\CodePool\ProductHelpers\CSOrderManager;
	use Doctrine\DBAL\Connection;
	use Doctrine\DBAL\DBALException;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	
	class RestController extends Controller {
		
		/**
		 * @Route("/shop/api/v1/products/get_variants/{aid}/{cid}/{pid}", name="rte_rest_get_variants", defaults={"pid"=null, "cid"=null})
		 * @var int $cid
		 * @var int $pid
		 * @var int $aid
		 * @return \Symfony\Component\HttpFoundation\JsonResponse
		 * @throws \Doctrine\DBAL\DBALException
		 */
		public function getProductVariantData($aid, $cid, $pid){
			/** @var Connection $conn */
			/** @var ProductBotProxy $prodProxy */
			$conn       = $this->getDoctrine()->getConnection();
			$dbDriver   = $conn->getParams()['driver'];
			$payLoad    = [];
			$prodProxy  = new ProductBotProxy();
			$sql        = $prodProxy->getSQL($dbDriver, $cid, $aid, $pid);
			$statement  = $conn->prepare($sql);
			$statement->execute();
			$resultSet  = $statement->fetchAll(\PDO::FETCH_CLASS, 'App\CodePool\Entity\ProductBotProxy');
			if($resultSet && sizeof($resultSet) > 0){
				$prodProxy  = $resultSet[0];
				$payLoad    = $prodProxy->initializeEntityBank();
				$orderPL    = ($obj = CSOrderManager::get_context_payload($cid, $pid, $aid)) ? get_object_vars($obj) : [
					'total'     =>'0.00',
					'cxt_total' =>'0.00',
					'qty'       =>'0',
					'cxt_qty'   =>'0',
				];
				$payLoad    = array_merge($payLoad, $orderPL);
			}
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($payLoad, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/add_to_store/{aid}/{cid}/{pid}/{price}/{qty}", name="rte_rest_add_to_store", defaults={"qty"=1})
		 * @var int $cid
		 * @var int $pid
		 * @var int $aid
		 * @var mixed $price
		 * @var int $qty
		 * @return \Symfony\Component\HttpFoundation\JsonResponse
		 */
		public function addOrderedItemToStore($aid, $cid, $pid, $price, $qty){
			if(!$aid || !$price || !$cid  || !$pid){
				try{
					$payLoad        = new \stdClass();
					$payLoad->cart  = CSCartHelper::render_cart("mini");
					return new JsonResponse($payLoad);
				}catch (\Exception $e){}
			}
			$payLoad        = CSOrderManager::add_order_to_store($cid, $pid, $aid, $price, $qty);
			if(!$payLoad){
				$payLoad    = new \stdClass();
			}
			$payLoad->cart  = utf8_encode(CSCartHelper::render_cart("mini"));
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($payLoad, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/get_cart/{aid}/{cid}/{pid}", name="rte_rest_get_shopping_cart")
		 * @var int $cid
		 * @var int $pid
		 * @var int $aid
		 * @var mixed $price
		 * @var int $qty
		 * @return \Symfony\Component\HttpFoundation\JsonResponse
		 */
		public function getShoppingCart($aid, $cid, $pid){
			$payLoad        = CSOrderManager::get_context_payload($cid, $pid, $aid);
			if(!$payLoad){
				$payLoad = new \stdClass();
			}
			$payLoad->cart  = CSCartHelper::render_cart("mini");
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($payLoad, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/delete_from_store/{aid}/{cid}/{pid}/{price}/{qty}", name="rte_rest_delete_from_store", defaults={"qty"=1})
		 * @var int $cid
		 * @var int $pid
		 * @var int $aid
		 * @var mixed $price
		 * @var int $qty
		 * @return \Symfony\Component\HttpFoundation\JsonResponse
		 */
		public function deleteOrderedItemsFromStore($aid, $cid, $pid, $price, $qty){
			$payLoad        = CSOrderManager::delete_order_from_store($cid, $pid, $aid, $price, $qty);
			if(!$payLoad){
				$payLoad = new \stdClass();
			}
			$payLoad->cart  = CSCartHelper::render_cart("mini");
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($payLoad, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/delete_item_cluster/{aid}/{cid}/{pid}/{price}/{qty}", name="rte_rest_delete_item_cluster", defaults={"qty"=1})
		 * @var int $cid
		 * @var int $pid
		 * @var int $aid
		 * @var mixed $price
		 * @var int $qty
		 * @return \Symfony\Component\HttpFoundation\JsonResponse
		 */
		public function deleteItemCluster($aid, $cid, $pid, $price, $qty){
			$cart           = CSOrderManager::delete_all_of_same_aid($cid, $pid, $aid, $price, $qty);
			$payLoad        = new \stdClass();
			$payLoad->cart  = $cart;
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($payLoad, 200, $header);
		}
		
		
		
		/**
		 * @Route("/shop/api/v1/products/fetch_products_by_cid/{cid}", name="rte_rest_fetch_products_by_cid", defaults={"cid"=1})
		 * @param int $cid
		 * @param Request $request
		 *
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function fetchAllProductsByCategoryID(Request $request, $cid){
			/**@var \Doctrine\DBAL\Connection $conn*/
			$conn       = $this->getDoctrine()->getConnection();
			$dbDriver   = $conn->getParams()['driver'];
			$sql        = (new ProductBotProxy())->getSQL($dbDriver, $cid);
			
			try {
				$statement = $conn->prepare( $sql );
			} catch ( DBALException $e ) {
			}
			$statement->execute();
			$resultSet  = $statement->fetchAll(\PDO::FETCH_OBJ);
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($resultSet, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/fetch_single_product/{aid}/{cid}/{pid}", name="rte_rest_fetch_single_product", defaults={"aid"=null, "cid"=null} )
		 * @param int $aid
		 * @param int $pid
		 * @param Request $request
		 *
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function fetchSingleProduct(Request $request, $aid, $cid, $pid){
			/**@var \Doctrine\DBAL\Connection $conn*/
			$conn       = $this->getDoctrine()->getConnection();
			$dbDriver   = $conn->getParams()['driver'];
			$sql        = (new ProductBotProxy())->getSQL($dbDriver, $cid, $aid, $pid);
			try {
				$statement = $conn->prepare( $sql );
			} catch ( DBALException $e ) {
			}
			$statement->execute();
			$resultSet  = $statement->fetch(\PDO::FETCH_OBJ);
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($resultSet, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/fetch_menu_items", name="rte_rest_fetch_menu_items")
		 * @param Request $request
		 *
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function fetchCategoriesAsMenuItems(Request $request){
			/**@var \Doctrine\DBAL\Connection $conn*/
			$conn       = $this->getDoctrine()->getConnection();
			$sql        = "SELECT * FROM " . TBL_CATS . " WHERE published=:PB";
			try {
				$statement = $conn->prepare( $sql );
			} catch ( DBALException $e ) { }
			$statement->execute(['PB' => 1]);
			$resultSet  = $statement->fetchAll(\PDO::FETCH_OBJ);
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($resultSet, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/fetch_all_items", name="rte_rest_fetch_all_items")
		 * @param Request $request
		 *
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function fetchAllPublishedProducts(Request $request){
			/**@var \Doctrine\DBAL\Connection $conn*/
			$conn       = $this->getDoctrine()->getConnection();
			$dbDriver   = $conn->getParams()['driver'];
			$sql        = (new ProductBotProxy())->getSQL($dbDriver, null, null, null);
			try {
				$statement = $conn->prepare( $sql );
			} catch ( DBALException $e ) {
			}
			$statement->execute();
			$resultSet  = $statement->fetchAll(\PDO::FETCH_OBJ);
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse($resultSet, 200, $header);
		}
		
		/**
		 * @Route("/shop/api/v1/products/get_context_order_payload/{aid}/{cid}/{pid}", name="rte_rest_get_context_order_payload")
		 * @var int $cid
		 * @var int $pid
		 * @var int $aid
		 * @return \Symfony\Component\HttpFoundation\JsonResponse
		 */
		public function getContextPayload($aid, $cid, $pid) {
			$header = [
				'Access-Control-Allow-Origin'   => '*',
				'Access-Control-Allow-Methods'  => 'GET, POST, OPTIONS',
				'Access-Control-Max-Age'        => '86400',
				'Content-Type'                  => 'application/json',
			];
			return new JsonResponse(CSOrderManager::get_context_payload( $cid, $pid, $aid ), 200, $header);
		}
	}