<?php
	/**
	 * Created by PhpStorm.
	 * User: Poiz Campbell
	 */
	
	namespace App\CodePool\Entity;
	
	use App\CodePool\Traits\EntityHelper;
	
	class ProductBotProxy extends ProductBot {
		use EntityHelper;
		
		/**
		 * @var array;
		 */
		protected $entityBank;
		
		public function __construct() {
			$this->entityBank   = [];
		}
		
	}
	