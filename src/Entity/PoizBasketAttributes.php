<?php
	
	namespace App\Entity;
	

	use Doctrine\ORM\Mapping as ORM;
	use App\Traits\EntityHelper;

	/**
	 * PoizBasketAttributes
	 *
	 * @ORM\Table(name="poiz_basket_attributes", uniqueConstraints={@ORM\UniqueConstraint(name="id_2", columns={"id"})}, indexes={@ORM\Index(name="id", columns={"id"})})
	 * @ORM\Entity(repositoryClass="App\Repository\PoizBasketAttributesRepo")
	 */
	class PoizBasketAttributes {
		use EntityHelper;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer", nullable=false)
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="IDENTITY")
		 */
		private $id;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="prod_id", type="integer", nullable=false)
		 */
		private $prodId;

		/**
		 * @var boolean
		 *
		 * @ORM\Column(name="published", type="boolean", nullable=false)
		 */
		private $published = '1';

		/**
		 * @var float
		 *
		 * @ORM\Column(name="size", type="float", precision=10, scale=2, nullable=false)
		 */
		private $size = '1.00';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="color", type="string", length=64, nullable=false)
		 */
		private $color = 'BLACK';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="size_unit", type="string", length=64, nullable=false)
		 */
		private $sizeUnit = 'PCS';

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="manufacturer", type="integer", nullable=false)
		 */
		private $manufacturer = '1';

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="weight", type="integer", nullable=false)
		 */
		private $weight = '1';

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="ratings", type="integer", nullable=true)
		 */
		private $ratings = '10';

		/**
		 * @var float
		 *
		 * @ORM\Column(name="milage", type="float", precision=11, scale=2, nullable=true)
		 */
		private $milage = '0.00';

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="year", type="integer", nullable=true)
		 */
		private $year;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="sku", type="string", length=64, nullable=false)
		 */
		private $sku;

		/**
		 * @var string
		 * @ORM\OneToOne(targetEntity="App\Entity\PoizBasketPrices", mappedBy="_attributes")
		 */
		private $_prices;

		/**
		 * @var string
		 * @ORM\OneToOne(targetEntity="App\Entity\PoizBasketImages", mappedBy="_attributes")
		 */
		private $_images;

		/**
		 * @var string
		 * @ORM\OneToOne(targetEntity="App\Entity\PoizBasketManufacturers", mappedBy="_attributes")
		 */
		private $_maker;

		/**
		 * @var \Doctrine\Common\Collections\ArrayCollection
		 * @ORM\ManyToOne(targetEntity="App\Entity\PoizBasketProducts", inversedBy="_attributes")
		 * @ORM\JoinColumn(name="prod_id", referencedColumnName="id")
		 */
		private $_products;



		/**
		 * Get id
		 *
		 * @return integer
		 */
		public function getId() {
			return $this->id;
		}

		/**
		 * @param int $id
		 * @return PoizBasketAttributes
		 */
		public function setId($id) {
			$this->id = $id;

			return $this;
		}

		/**
		 * Get prodId
		 *
		 * @return integer
		 */
		public function getProdId() {
			return $this->prodId;
		}

		/**
		 * Set prodId
		 *
		 * @param integer $prodId
		 *
		 * @return PoizBasketAttributes
		 */
		public function setProdId($prodId) {
			$this->prodId = $prodId;

			return $this;
		}

		/**
		 * Get published
		 *
		 * @return boolean
		 */
		public function getPublished() {
			return $this->published;
		}

		/**
		 * Set published
		 *
		 * @param boolean $published
		 *
		 * @return PoizBasketAttributes
		 */
		public function setPublished($published) {
			$this->published = $published;

			return $this;
		}

		/**
		 * Get size
		 *
		 * @return float
		 */
		public function getSize() {
			return $this->size;
		}

		/**
		 * Set size
		 *
		 * @param float $size
		 *
		 * @return PoizBasketAttributes
		 */
		public function setSize($size) {
			$this->size = $size;

			return $this;
		}

		/**
		 * Get color
		 *
		 * @return string
		 */
		public function getColor() {
			return $this->color;
		}

		/**
		 * Set color
		 *
		 * @param string $color
		 *
		 * @return PoizBasketAttributes
		 */
		public function setColor($color) {
			$this->color = $color;

			return $this;
		}

		/**
		 * Get sizeUnit
		 *
		 * @return string
		 */
		public function getSizeUnit() {
			return $this->sizeUnit;
		}

		/**
		 * Set sizeUnit
		 *
		 * @param string $sizeUnit
		 *
		 * @return PoizBasketAttributes
		 */
		public function setSizeUnit($sizeUnit) {
			$this->sizeUnit = $sizeUnit;

			return $this;
		}

		/**
		 * Get manufacturer
		 *
		 * @return integer
		 */
		public function getManufacturer() {
			return $this->manufacturer;
		}

		/**
		 * Set manufacturer
		 *
		 * @param integer $manufacturer
		 *
		 * @return PoizBasketAttributes
		 */
		public function setManufacturer($manufacturer) {
			$this->manufacturer = $manufacturer;

			return $this;
		}

		/**
		 * Get weight
		 *
		 * @return integer
		 */
		public function getWeight() {
			return $this->weight;
		}

		/**
		 * Set weight
		 *
		 * @param integer $weight
		 *
		 * @return PoizBasketAttributes
		 */
		public function setWeight($weight) {
			$this->weight = $weight;

			return $this;
		}

		/**
		 * Get ratings
		 *
		 * @return integer
		 */
		public function getRatings() {
			return $this->ratings;
		}

		/**
		 * Set ratings
		 *
		 * @param integer $ratings
		 *
		 * @return PoizBasketAttributes
		 */
		public function setRatings($ratings) {
			$this->ratings = $ratings;

			return $this;
		}

		/**
		 * Get milage
		 *
		 * @return float
		 */
		public function getMilage() {
			return $this->milage;
		}

		/**
		 * Set milage
		 *
		 * @param float $milage
		 *
		 * @return PoizBasketAttributes
		 */
		public function setMilage($milage) {
			$this->milage = $milage;

			return $this;
		}

		/**
		 * Get year
		 *
		 * @return integer
		 */
		public function getYear() {
			return $this->year;
		}

		/**
		 * Set year
		 *
		 * @param integer $year
		 *
		 * @return PoizBasketAttributes
		 */
		public function setYear($year) {
			$this->year = $year;

			return $this;
		}

		/**
		 * Get sku
		 *
		 * @return string
		 */
		public function getSku() {
			return $this->sku;
		}

		/**
		 * Set sku
		 *
		 * @param string $sku
		 *
		 * @return PoizBasketAttributes
		 */
		public function setSku($sku) {
			$this->sku = $sku;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getPrices() {
			return $this->_prices;
		}

		/**
		 * @param string $prices
		 * @return PoizBasketAttributes
		 */
		public function setPrices($prices) {
			$this->_prices = $prices;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getImages() {
			return $this->_images;
		}

		/**
		 * @param string $images
		 * @return PoizBasketAttributes
		 */
		public function setImages($images) {
			$this->_images = $images;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getMaker() {
			return $this->_maker;
		}

		/**
		 * @param string $maker
		 * @return PoizBasketAttributes
		 */
		public function setMaker($maker) {
			$this->_maker = $maker;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getProducts() {
			return $this->_products;
		}

		/**
		 * @param string $products
		 * @return PoizBasketAttributes
		 */
		public function setProducts($products) {
			$this->_products = $products;

			return $this;
		}

	}
