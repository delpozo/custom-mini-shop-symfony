<?php

	namespace App\Entity;

	use App\Traits\EntityHelper;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * PoizBasketPrices
	 *
	 * @ORM\Table(name="poiz_basket_prices", indexes={@ORM\Index(name="id", columns={"id"})})
	 * @ORM\Entity(repositoryClass="App\Repository\PoizBasketPricesRepo")
	 */
	class PoizBasketPrices {
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
		 * @var integer
		 *
		 * @ORM\Column(name="attrib_id", type="integer", nullable=false)
		 */
		private $attribId;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="unit", type="integer", nullable=false)
		 */
		private $unit = '1';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=false)
		 */
		private $price;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="discount_price", type="decimal", precision=10, scale=2, nullable=false)
		 */
		private $discountPrice;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="normal_price", type="decimal", precision=10, scale=2, nullable=false)
		 */
		private $normalPrice;

		/**
		 * @var boolean
		 *
		 * @ORM\Column(name="published", type="boolean", nullable=false)
		 */
		private $published = '1';

		/**
		 * @var \DateTime
		 *
		 * @ORM\Column(name="publish_up", type="datetime", nullable=false)
		 */
		private $publishUp = 'CURRENT_TIMESTAMP';

		/**
		 * @var \DateTime
		 *
		 * @ORM\Column(name="publish_down", type="datetime", nullable=false)
		 */
		private $publishDown = '0000-00-00 00:00:00';

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\PoizBasketProducts", inversedBy="_prices")
		 * @ORM\JoinColumn(name="prod_id", referencedColumnName="id")
		 */
		private $_products;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\PoizBasketAttributes", inversedBy="_prices")
		 * @ORM\JoinColumn(name="attrib_id", referencedColumnName="id")
		 */
		private $_attributes;



		/**
		 * PoizBasketPrices constructor.
		 */
		public function __construct() {
		}


		/**
		 * Get id
		 *
		 * @return integer
		 */
		public function getId() {
			return $this->id;
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
		 * Get attribId
		 *
		 * @return integer
		 */
		public function getAttribId() {
			return $this->attribId;
		}

		/**
		 * Get unit
		 *
		 * @return integer
		 */
		public function getUnit() {
			return $this->unit;
		}

		/**
		 * Get price
		 *
		 * @return string
		 */
		public function getPrice() {
			return $this->price;
		}

		/**
		 * Get discountPrice
		 *
		 * @return string
		 */
		public function getDiscountPrice() {
			return $this->discountPrice;
		}

		/**
		 * Get normalPrice
		 *
		 * @return string
		 */
		public function getNormalPrice() {
			return $this->normalPrice;
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
		 * Get publishUp
		 *
		 * @return \DateTime
		 */
		public function getPublishUp() {
			return $this->publishUp;
		}

		/**
		 * Get publishDown
		 *
		 * @return \DateTime
		 */
		public function getPublishDown() {
			return $this->publishDown;
		}

		/**
		 * @return mixed
		 */
		public function getProducts() {
			return $this->_products;
		}

		/**
		 * @return mixed
		 */
		public function getAttributes() {
			return $this->_attributes;
		}



		/**
		 * @param int $id
		 */
		public function setId($id) {
			$this->id = $id;
		}

		/**
		 * Set prodId
		 *
		 * @param integer $prodId
		 *
		 * @return PoizBasketPrices
		 */
		public function setProdId($prodId) {
			$this->prodId = $prodId;

			return $this;
		}

		/**
		 * Set attribId
		 *
		 * @param integer $attribId
		 *
		 * @return PoizBasketPrices
		 */
		public function setAttribId($attribId) {
			$this->attribId = $attribId;

			return $this;
		}

		/**
		 * Set unit
		 *
		 * @param integer $unit
		 *
		 * @return PoizBasketPrices
		 */
		public function setUnit($unit) {
			$this->unit = $unit;

			return $this;
		}

		/**
		 * Set price
		 *
		 * @param string $price
		 *
		 * @return PoizBasketPrices
		 */
		public function setPrice($price) {
			$this->price = $price;

			return $this;
		}

		/**
		 * Set discountPrice
		 *
		 * @param string $discountPrice
		 *
		 * @return PoizBasketPrices
		 */
		public function setDiscountPrice($discountPrice) {
			$this->discountPrice = $discountPrice;

			return $this;
		}

		/**
		 * Set normalPrice
		 *
		 * @param string $normalPrice
		 *
		 * @return PoizBasketPrices
		 */
		public function setNormalPrice($normalPrice) {
			$this->normalPrice = $normalPrice;

			return $this;
		}

		/**
		 * Set published
		 *
		 * @param boolean $published
		 *
		 * @return PoizBasketPrices
		 */
		public function setPublished($published) {
			$this->published = $published;

			return $this;
		}

		/**
		 * Set publishUp
		 *
		 * @param \DateTime $publishUp
		 *
		 * @return PoizBasketPrices
		 */
		public function setPublishUp($publishUp) {
			$this->publishUp = $publishUp;

			return $this;
		}

		/**
		 * Set publishDown
		 *
		 * @param \DateTime $publishDown
		 *
		 * @return PoizBasketPrices
		 */
		public function setPublishDown($publishDown) {
			$this->publishDown = $publishDown;

			return $this;
		}

		/**
		 * @param mixed $products
		 */
		public function setProducts($products) {
			$this->_products = $products;
		}

		/**
		 * @param mixed $attributes
		 */
		public function setAttributes($attributes) {
			$this->_attributes = $attributes;
		}

	}
