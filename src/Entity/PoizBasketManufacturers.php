<?php

	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use App\Traits\EntityHelper;

	/**
	 * PoizBasketManufacturers
	 *
	 * @ORM\Table(name="poiz_basket_manufacturers", uniqueConstraints={@ORM\UniqueConstraint(name="id_2", columns={"id"})}, indexes={@ORM\Index(name="id", columns={"id"}), @ORM\Index(name="id_3", columns={"id"})})
	 * @ORM\Entity(repositoryClass="App\Repository\PoizBasketManufacturersRepo")
	 */
	class PoizBasketManufacturers {
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
		 * @var string
		 *
		 * @ORM\Column(name="title", type="string", length=255, nullable=false)
		 */
		private $title = 'Undisclosed';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="logo", type="string", length=255, nullable=false)
		 */
		private $logo = '/images/icons/man_icons/default.jpg';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="description", type="text", length=65535, nullable=false)
		 */
		private $description;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\PoizBasketAttributes", inversedBy="_maker")
		 */
		private $_attributes;

		/**
		 * PoizBasketManufacturers constructor.
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
		 * Get title
		 *
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Get logo
		 *
		 * @return string
		 */
		public function getLogo() {
			return $this->logo;
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
		 * Set title
		 *
		 * @param string $title
		 *
		 * @return PoizBasketManufacturers
		 */
		public function setTitle($title) {
			$this->title = $title;

			return $this;
		}

		/**
		 * Set logo
		 *
		 * @param string $logo
		 *
		 * @return PoizBasketManufacturers
		 */
		public function setLogo($logo) {
			$this->logo = $logo;

			return $this;
		}

		/**
		 * Set description
		 *
		 * @param string $description
		 *
		 * @return PoizBasketManufacturers
		 */
		public function setDescription($description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * @param mixed $attributes
		 */
		public function setAttributes($attributes) {
			$this->_attributes = $attributes;
		}

	}
