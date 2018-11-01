<?php
	/**
	 * Created by PhpStorm.
	 * User: Poiz Campbell
	 */
	
	namespace App\Controller;
	
	
	use App\CodePool\Entity\ProductBotProxy;
	use App\CodePool\ProductHelpers\CSCartHelper;
	use App\CodePool\ProductHelpers\CSOrderManager;
	use Doctrine\DBAL\Connection;
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
			return new JsonResponse($payLoad);
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
			return new JsonResponse($payLoad);
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
			return new JsonResponse($payLoad);
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
			return new JsonResponse($payLoad);
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
			return new JsonResponse($payLoad);
		}
	}