<?php

	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use App\Traits\EntityHelper;

	/**
	 * PoizBasketCategories
	 *
	 * @ORM\Table(name="poiz_basket_categories", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="id_2", columns={"id"})})
	 * @ORM\Entity(repositoryClass="App\Repository\PoizBasketCategoriesRepo")
	 */
	class PoizBasketCategories {
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
		 * @var string
		 *
		 * @ORM\Column(name="title", type="string", length=255, nullable=false)
		 */
		private $title;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="prefix", type="string", length=64, nullable=false)
		 */
		private $prefix;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="alias", type="string", length=255, nullable=false)
		 */
		private $alias;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="icon", type="string", length=255, nullable=false)
		 */
		private $icon = 'images/cat_icons/default.png';

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
		 * @ORM\Column(name="params", type="text", length=65535, nullable=true)
		 */
		private $params;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\PoizBasketProducts", mappedBy="category")
		 * @ORM\JoinColumn(name="id", referencedColumnName="prod_id")
		 */
		private $products;

		/**
		 * @var array
		 */
		private $properties;

		/**
		 * PoizBasketCategories constructor.
		 */
		public function __construct() {
			$this->properties   = $this->getClassProperties($this);
			var_dump($this->properties);
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
		 * Get ordering
		 *
		 * @return integer
		 */
		public function getOrdering() {
			return $this->ordering;
		}

		/**
		 * Set ordering
		 *
		 * @param integer $ordering
		 *
		 * @return PoizBasketCategories
		 */
		public function setOrdering($ordering) {
			$this->ordering = $ordering;

			return $this;
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
		 * Set publishUp
		 *
		 * @param \DateTime $publishUp
		 *
		 * @return PoizBasketCategories
		 */
		public function setPublishUp($publishUp) {
			$this->publishUp = $publishUp;

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
		 * @return PoizBasketCategories
		 */
		public function setPublished($published) {
			$this->published = $published;

			return $this;
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
		 * Set title
		 *
		 * @param string $title
		 *
		 * @return PoizBasketCategories
		 */
		public function setTitle($title) {
			$this->title = $title;

			return $this;
		}

		/**
		 * Get prefix
		 *
		 * @return string
		 */
		public function getPrefix() {
			return $this->prefix;
		}

		/**
		 * Set prefix
		 *
		 * @param string $prefix
		 *
		 * @return PoizBasketCategories
		 */
		public function setPrefix($prefix) {
			$this->prefix = $prefix;

			return $this;
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
		 * Set alias
		 *
		 * @param string $alias
		 *
		 * @return PoizBasketCategories
		 */
		public function setAlias($alias) {
			$this->alias = $alias;

			return $this;
		}

		/**
		 * Get icon
		 *
		 * @return string
		 */
		public function getIcon() {
			return $this->icon;
		}

		/**
		 * Set icon
		 *
		 * @param string $icon
		 *
		 * @return PoizBasketCategories
		 */
		public function setIcon($icon) {
			$this->icon = $icon;

			return $this;
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
		 * Set publishDown
		 *
		 * @param \DateTime $publishDown
		 *
		 * @return PoizBasketCategories
		 */
		public function setPublishDown($publishDown) {
			$this->publishDown = $publishDown;

			return $this;
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
		 * Set description
		 *
		 * @param string $description
		 *
		 * @return PoizBasketCategories
		 */
		public function setDescription($description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * Get params
		 *
		 * @return string
		 */
		public function getParams() {
			return $this->params;
		}

		/**
		 * Set params
		 *
		 * @param string $params
		 *
		 * @return PoizBasketCategories
		 */
		public function setParams($params) {
			$this->params = $params;

			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getProducts() {
			return $this->products;
		}

		/**
		 * @param mixed $products
		 * @return PoizBasketCategories
		 */
		public function setProducts($products) {
			$this->products = $products;

			return $this;
		}
	}
