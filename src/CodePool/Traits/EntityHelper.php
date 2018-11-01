<?php
	/**
	 * Author: Poiz Campbell
	 */
	
	namespace App\CodePool\Traits;
	

	trait EntityHelper {

		public function autoSetClassProps2($props){
			if( (is_array($props) || is_object($props)) && $props ){
				foreach($props as $propName=>$propValue){
					$gsName                     = $this->rinseFieldName($propName);
					$setterMethod               = "set" . $gsName;
					$getterMethod1              = "get" . $gsName;
					$getterMethod2              = "get" . $gsName;
					if(property_exists($this, $propName)){
						$this->$setterMethod($propValue);
					}
					if(method_exists($this, $getterMethod1)){
						$this->entityBank[$propName] = $this->$getterMethod1($propValue);
					}elseif(method_exists($this, $getterMethod2)){
						$this->entityBank[$propName] = $this->$getterMethod2($propValue);
					}
					unset($this->entityBank["entityBank"]);
				}
			}
		}

		public function autoSetClassProps($props){
			if( (is_array($props) || is_object($props)) && $props ){
				foreach($props as $propName=>$propValue){
					$gsName                     = $this->rinseFieldName($propName);
					$setterMethod               = "set" . $gsName;
					if(property_exists($this, $propName)){
						if(method_exists($this, $setterMethod)){
							$this->$setterMethod($propValue);
						}else{
							$this->$propName    = $propValue;
						}
						$this->entityBank[$propName]    = $propValue;
					}
				}
			}
		}

		/**
		 * Generate a Simple Random Hex-Hash for use in AJAX... for calculating some Auth. Values
		 * @param int $length
		 * @return string
		 */
		protected function generateRandomHash($length = 6) {
			$characters     = '0123456789ABCDEF';
			$randomString   = '';

			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}

			return $randomString;
		}

		protected function rinseFieldName($fieldName){
			$arrName    = preg_split("#[_\-\s]+#",    $fieldName);
			$arrName    = array_map("ucfirst", $arrName);;
			$strName    = implode("", $arrName);
			return $strName;
		}

		public function autoSetClassProperties($arrData){
			if(!is_null($arrData)){
				foreach($arrData as $prop=>$val){
					if(property_exists($this, $prop)){
						if($prop == 'id'){

						}elseif($prop == ''){

						}
						$this->$prop    = $val;
						$this->entityBank[$prop]    = $val;
						unset($this->entityBank["entityBank"]);
					}
				}
			}
		}

		protected function getClassProperties($fullyQualifiedClassName){
			$arrClassProps                  = [];
			$refClass                       = new \ReflectionClass($fullyQualifiedClassName);

			foreach ($refClass->getProperties() as &$refProperty) {
				$arrClassProps[]        = $refProperty->getName();
			}
			return $arrClassProps;
		}

		public function initializeEntityBank(){
			$refClass					= new \ReflectionClass($this);
			foreach ($refClass->getProperties() as &$refProperty) {
				$key					= $refProperty->getName();
				if($key == "entityBank"){ continue; }
				$this->entityBank[$key]	= $this->$key;
			}
			return $this->entityBank;
		}

		public function initializeProperties($object){
			foreach ($object as $prop=>$propVal) {
				if(property_exists($this, $prop)){
					if($prop == "entityBank" || preg_match("#^_#", $prop)){ continue; }
					$this->$prop                = $propVal;
					$this->entityBank[$prop]	= $propVal;
				}
			}
			return $this;
		}

		public static function initializeClassProperties($object){

		}

		public function __get($name) {
			if(property_exists($this, $name)){
				return $this->$name;
			}else{
				if(array_key_exists($name, $this->entityBank)){
					return $this->entityBank[$name];
				}
			}
			return null;
		}

		public function __set($name, $value) {
			if(property_exists($this, $name)){
				$this->$name     = $value;
				if($name == 'entityBank'){
					if(!empty($value)){
						$this->autoSetClassProps($value);
					}
				}else{
					$this->entityBank[$name]   = $value;
				}
			}else{
				$this->entityBank[$name]   = $value;
			}
			return $this;
		}

		public function __clone() {
			return $this;
		}
		
		public function getEntityBank() {
			if(empty($this->entityBank)){
				$this->initializeEntityBank();
			}
			return $this->entityBank;
		}

		public function setEntityBank($entityBank) {
			$this->entityBank = $entityBank;

			return $this;
		}


	}