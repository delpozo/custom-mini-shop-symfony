<?php
	/**
	 * Created by PhpStorm.Author
	 */

	namespace App\CodePool;

	class _DEFINITIONS_{}

	defined("CODE_POOL")        or define("CODE_POOL",          __DIR__);
	defined("WEB_ROOT")         or define("WEB_ROOT",           __DIR__     . "/../../");
	defined("APP_ROOT")         or define("APP_ROOT",           __DIR__     . "/../../");
	defined("SRC_ROOT")         or define("SRC_ROOT",           __DIR__     . "/../");
	defined("ENTITY_ROOT")      or define("ENTITY_ROOT",        APP_ROOT    . "Entity");

	//TABLE DEFINITIONS:
	defined("TBL_PREFIX")       or define("TBL_PREFIX",                       "poiz_");
	defined("TBL_USER")         or define("TBL_USER",                         "user");
	defined("TBL_ROLE")         or define("TBL_ROLE",                         "role");
	defined("TBL_USER_PROFILE") or define("TBL_USER_PROFILE",                 "user_profile");
	defined("TBL_USER_GROUPS")  or define("TBL_USER_GROUPS",                  "user_groups");
	defined("TBL_USER_GROUP_M") or define("TBL_USER_GROUP_M",                 "user_group_maps");
	defined("TBL_CLIENTS")      or define("TBL_CLIENTS",        TBL_PREFIX  . "basket_clients");
	defined("TBL_PRODUCTS")     or define("TBL_PRODUCTS",       TBL_PREFIX  . "basket_products");
	defined("TBL_IMAGES")       or define("TBL_IMAGES",         TBL_PREFIX  . "basket_images");
	defined("TBL_PRICES")       or define("TBL_PRICES",         TBL_PREFIX  . "basket_prices");
	defined("TBL_ATTRIBUTES")   or define("TBL_ATTRIBUTES",     TBL_PREFIX  . "basket_attributes");
	defined("TBL_MAKERS")       or define("TBL_MAKERS",         TBL_PREFIX  . "basket_manufacturers");
	defined("TBL_CATS")         or define("TBL_CATS",           TBL_PREFIX  . "basket_categories");
	defined("TBL_S_CATS")       or define("TBL_S_CATS",         TBL_PREFIX  . "basket_subcategories");


