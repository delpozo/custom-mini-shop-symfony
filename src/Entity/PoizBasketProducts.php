<?php

	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use App\Traits\EntityHelper;

	/**
	 * PoizBasketProducts
	 *
	 * @ORM\Table(name="poiz_basket_products", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="id_2", columns={"id"})})
	 * @ORM\Entity(repositoryClass="App\Repository\PoizBasketProductsRepo")
	 */
	class PoizBasketProducts {
		//use EntityHelper;
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
		 * @ORM\Column(name="cat_id", type="integer", nullable=false)
		 *
		 */
		private $catId;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="sub_cat_id", type="integer", nullable=false)
		 */
		private $subCatId;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="ordering", type="integer", nullable=false)
		 */
		private $ordering;

		/**
		 * @var boolean
		 *
		 * @ORM\Column(name="published", type="boolean", nullable=false)
		 */
		private $published = '1';

		/**
		 * @var boolean
		 *
		 * @ORM\Column(name="sold_on_discount", type="boolean", nullable=false)
		 */
		private $soldOnDiscount = '0';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="title", type="string", length=255, nullable=false)
		 */
		private $title;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="sku", type="string", length=128, nullable=false)
		 */
		private $sku;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="codename", type="string", length=128, nullable=false)
		 */
		private $codename;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="alias", type="string", length=255, nullable=false)
		 */
		private $alias;

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
		 * @var string
		 *
		 * @ORM\Column(name="description", type="text", length=65535, nullable=false)
		 */
		private $description;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="variants", type="blob", length=65535, nullable=false)
		 */
		private $variants;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\PoizBasketCategories", inversedBy="products")
		 * @ORM\JoinColumn(name="cat_id", referencedColumnName="id")
		 */
		private $category;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\PoizBasketPrices", mappedBy="_products")
		 * @ORM\JoinColumn(name="id", referencedColumnName="prod_id")
		 */
		private $_prices;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\PoizBasketImages", mappedBy="_products")
		 * @ORM\JoinColumn(name="id", referencedColumnName="prod_id")
		 */
		private $_images;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\PoizBasketAttributes", mappedBy="_products")
		 * @ORM\JoinColumn(name="id", referencedColumnName="prod_id")
		 */
		private $_attributes;



		/**
		 * PoizBasketProducts constructor.
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
		 * Get catId
		 *
		 * @return integer
		 */
		public function getCatId() {
			return $this->catId;
		}

		/**
		 * Get subCatId
		 *
		 * @return integer
		 */
		public function getSubCatId() {
			return $this->subCatId;
		}

		/**
		 * Get ordering
		 *
		 * @return integer
		 */
		public function getOrdering() {
			return $this->ordering;
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
		 * Get soldOnDiscount
		 *
		 * @return boolean
		 */
		public function getSoldOnDiscount() {
			return $this->soldOnDiscount;
		}

		/**
		 * Get title
		 *
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
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
		 * Get codename
		 *
		 * @return string
		 */
		public function getCodename() {
			return $this->codename;
		}

		/**
		 * Get alias
		 *
		 * @return string
		 */
		public function getAlias() {
			return $this->alias;
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
		 * Get description
		 *
		 * @return string
		 */
		public function getDescription() {
			return $this->description;
		}

		/**
		 * Get variants
		 *
		 * @return string
		 */
		public function getVariants() {
			return $this->variants;
		}

		/**
		 * @return mixed
		 */
		public function getAttributes() {
			return $this->_attributes;
		}

		/**
		 * @return mixed
		 */
		public function getImages() {
			return $this->_images;
		}

		/**
		 * @return mixed
		 */
		public function getPrices() {
			return $this->_prices;
		}

		/**
		 * @return mixed
		 */
		public function getCategory() {
			return $this->category;
		}



		/**
		 * @param int $id
		 * @return PoizBasketProducts
		 */
		public function setId($id) {
			$this->id = $id;

			return $this;
		}

		/**
		 * Set catId
		 *
		 * @param integer $catId
		 *
		 * @return PoizBasketProducts
		 */
		public function setCatId($catId) {
			$this->catId = $catId;

			return $this;
		}

		/**
		 * Set subCatId
		 *
		 * @param integer $subCatId
		 *
		 * @return PoizBasketProducts
		 */
		public function setSubCatId($subCatId) {
			$this->subCatId = $subCatId;

			return $this;
		}

		/**
		 * Set ordering
		 *
		 * @param integer $ordering
		 *
		 * @return PoizBasketProducts
		 */
		public function setOrdering($ordering) {
			$this->ordering = $ordering;

			return $this;
		}

		/**
		 * Set published
		 *
		 * @param boolean $published
		 *
		 * @return PoizBasketProducts
		 */
		public function setPublished($published) {
			$this->published = $published;

			return $this;
		}

		/**
		 * Set soldOnDiscount
		 *
		 * @param boolean $soldOnDiscount
		 *
		 * @return PoizBasketProducts
		 */
		public function setSoldOnDiscount($soldOnDiscount) {
			$this->soldOnDiscount = $soldOnDiscount;

			return $this;
		}

		/**
		 * Set title
		 *
		 * @param string $title
		 *
		 * @return PoizBasketProducts
		 */
		public function setTitle($title) {
			$this->title = $title;

			return $this;
		}

		/**
		 * Set sku
		 *
		 * @param string $sku
		 *
		 * @return PoizBasketProducts
		 */
		public function setSku($sku) {
			$this->sku = $sku;

			return $this;
		}

		/**
		 * Set codename
		 *
		 * @param string $codename
		 *
		 * @return PoizBasketProducts
		 */
		public function setCodename($codename) {
			$this->codename = $codename;

			return $this;
		}

		/**
		 * Set alias
		 *
		 * @param string $alias
		 *
		 * @return PoizBasketProducts
		 */
		public function setAlias($alias) {
			$this->alias = $alias;

			return $this;
		}

		/**
		 * Set publishUp
		 *
		 * @param \DateTime $publishUp
		 *
		 * @return PoizBasketProducts
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
		 * @return PoizBasketProducts
		 */
		public function setPublishDown($publishDown) {
			$this->publishDown = $publishDown;

			return $this;
		}

		/**
		 * Set description
		 *
		 * @param string $description
		 *
		 * @return PoizBasketProducts
		 */
		public function setDescription($description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * Set variants
		 *
		 * @param string $variants
		 *
		 * @return PoizBasketProducts
		 */
		public function setVariants($variants) {
			$this->variants = $variants;

			return $this;
		}

		/**
		 * @param mixed $attributes
		 * @return PoizBasketProducts
		 */
		public function setAttributes($attributes) {
			$this->_attributes = $attributes;

			return $this;
		}

		/**
		 * @param mixed $images
		 * @return PoizBasketProducts
		 */
		public function setImages($images) {
			$this->_images = $images;

			return $this;
		}

		/**
		 * @param mixed $prices
		 * @return PoizBasketProducts
		 */
		public function setPrices($prices) {
			$this->_prices = $prices;

			return $this;
		}

		/**
		 * @param mixed $category
		 * @return PoizBasketProducts
		 */
		public function setCategory($category) {
			$this->category = $category;

			return $this;
		}
	}