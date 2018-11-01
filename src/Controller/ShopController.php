<?php
	/**
	 * Created by PhpStorm.
	 * User: Poiz Campbell
	 */
	
	namespace App\Controller;
	
	require_once __DIR__ . '/../CodePool/_DEFINITIONS_.php';
	
	use App\CodePool\Entity\ProductBotProxy;
	use Doctrine\DBAL\DBALException;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	
	class ShopController extends Controller {
		
		/**
		 * @Route("/", name="rte_home")
		 *
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function mainPageAction(Request $request){
			return $this->productsAction($request, 1);
		}
		
		/**
		 * @Route({
		 *
		 *     "en": "/shop/products/{cid}/{alias}",
		 *     "de": "/shop/products/{cid}/{alias}",
		 *     "fr": "/shop/products/{cid}/{alias}"
		 * }, name="rte_shop", defaults={"cid"=1, "alias"="generic"})
		 *
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function productsAction(Request $request, $cid){
			/**@var \Doctrine\DBAL\Connection $conn*/
			$conn       = $this->getDoctrine()->getConnection();
			$dbDriver   = $conn->getParams()['driver'];
			$sql        = (new ProductBotProxy())->getSQL($dbDriver, $cid);    // , $AID, $PID
			
			try {
				$statement = $conn->prepare( $sql );
			} catch ( DBALException $e ) {
			}
			$statement->execute();
			$resultSet  = $statement->fetchAll(\PDO::FETCH_CLASS, 'App\CodePool\Entity\ProductBotProxy');
			
			return $this->render('category-products.html.twig', ['products'=>$resultSet]);
		}
		
		/**
		 * @Route({
		 *
		 *     "en": "/shop/{alias}/{aid}/{pid}/{cid}",
		 *     "fr": "/shop/{alias}/{aid}/{pid}/{cid}",
		 *     "de": "/shop/{alias}/{aid}/{pid}/{cid}"
		 * }, name="rte_product_details", defaults={"pid"=null, "cid"=null})
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function productDetailsAction(Request $request, $aid, $pid, $cid){
			/**@var \Doctrine\DBAL\Connection $conn*/
			$conn       = $this->getDoctrine()->getConnection();
			$dbDriver   = $conn->getParams()['driver'];
			$sql        = (new ProductBotProxy())->getSQL($dbDriver, $cid, $aid, $pid);
			try {
				$statement = $conn->prepare( $sql );
			} catch ( DBALException $e ) {
			}
			$statement->execute();
			$resultSet  = $statement->fetchAll(\PDO::FETCH_CLASS, 'App\CodePool\Entity\ProductBotProxy');
			return $this->render('product-detail.html.twig' , ['products'=>$resultSet]);
		}
		
	}