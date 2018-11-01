<?php
	/**
	 * Created by JetBrains PhpStorm.
	 * User: Poiz Campbell
	 */

	namespace App\CodePool\ProductHelpers;


	class CSCurrency implements iCSCurrency {
		protected $active_currency;

		public static function get_active_currency () {
			return "CHF";      //"&euro;";
		}


	}


