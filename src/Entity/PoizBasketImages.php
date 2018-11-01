<?php

	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use App\Traits\EntityHelper;

	/**
	 * PoizBasketImages
	 *
	 * @ORM\Table(name="poiz_basket_images", uniqueConstraints={@ORM\UniqueConstraint(name="id_2", columns={"id"})}, indexes={@ORM\Index(name="id", columns={"id"})})
	 * @ORM\Entity(repositoryClass="App\Repository\PoizBasketImagesRepo")
	 */
	class PoizBasketImages {
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
		 * @ORM\Column(name="hash", type="string", length=128, nullable=false)
		 */
		private $hash;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="alias", type="string", length=255, nullable=false)
		 */
		private $alias;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="pix", type="string", length=255, nullable=false)
		 */
		private $pix;

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
		 * @ORM\ManyToOne(targetEntity="App\Entity\PoizBasketProducts", inversedBy="_images")
		 * @ORM\JoinColumn(name="prod_id", referencedColumnName="id")
		 */
		private $_products;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\PoizBasketAttributes", inversedBy="_images")
		 * @ORM\JoinColumn(name="attrib_id", referencedColumnName="id")
		 */
		private $_attributes;



		/**
		 * PoizBasketImages constructor.
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
		 * @param int $id
		 */
		public function setId($id) {
			$this->id = $id;
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
		 * @return PoizBasketImages
		 */
		public function setProdId($prodId) {
			$this->prodId = $prodId;

			return $this;
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
		 * Set attribId
		 *
		 * @param integer $attribId
		 *
		 * @return PoizBasketImages
		 */
		public function setAttribId($attribId) {
			$this->attribId = $attribId;

			return $this;
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
		 * @return PoizBasketImages
		 */
		public function setOrdering($ordering) {
			$this->ordering = $ordering;

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
		 * @return PoizBasketImages
		 */
		public function setPublished($published) {
			$this->published = $published;

			return $this;
		}

		/**
		 * Get hash
		 *
		 * @return string
		 */
		public function getHash() {
			return $this->hash;
		}

		/**
		 * Set hash
		 *
		 * @param string $hash
		 *
		 * @return PoizBasketImages
		 */
		public function setHash($hash) {
			$this->hash = $hash;

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
		 * @return PoizBasketImages
		 */
		public function setAlias($alias) {
			$this->alias = $alias;

			return $this;
		}

		/**
		 * Get pix
		 *
		 * @return string
		 */
		public function getPix() {
			return $this->pix;
		}

		/**
		 * Set pix
		 *
		 * @param string $pix
		 *
		 * @return PoizBasketImages
		 */
		public function setPix($pix) {
			$this->pix = $pix;

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
		 * @return PoizBasketImages
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
		 * @return PoizBasketImages
		 */
		public function setDescription($description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getProducts() {
			return $this->_products;
		}

		/**
		 * @param mixed $products
		 */
		public function setProducts($products) {
			$this->_products = $products;
		}

		/**
		 * @return mixed
		 */
		public function getAttributes() {
			return $this->_attributes;
		}

		/**
		 * @param mixed $attributes
		 */
		public function setAttributes($attributes) {
			$this->_attributes = $attributes;
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
		 * @return PoizBasketImages
		 */
		public function setPublishUp($publishUp) {
			$this->publishUp = $publishUp;

			return $this;
		}

	}
