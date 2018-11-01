<?php
/**
 * Created by PhpStorm.
 * User: Poiz Campbell
 */

namespace App\CodePool\Helpers\Builders\InterFaces;


interface PoizProductInterface {
	function getOnSale();
	function getProductID();
	function getProductAID();
	function getProductCID();
	function getProductAlias();
	function getProductTitle();
	function getSalePrice();
	function getNormalPrice();
	function getProductPix();
	function getActiveCurrency();
	function getOrderQuantity();
	function getVariantImages();
	function getVariantAIDS();
	function getVariantPrices();
	function getVariantSalePrices();
	function getVariantSaleStates();
	function getVariantColors();
	function getOrderTotalValue();
	function getProductDescription();
	static function getRGBTranspose($color);
}