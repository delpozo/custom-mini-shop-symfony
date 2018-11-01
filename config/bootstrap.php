<?php
	// bootstrap.php
	use Doctrine\ORM\Tools\Setup;
	use Doctrine\ORM\EntityManager;
	use Doctrine\Common\Cache\ArrayCache;
	use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
	use Doctrine\Common\Annotations\AnnotationReader;
	use Symfony\Component\Dotenv\Dotenv;
	
	//INCLUDE AUTO-LOADER IN THE PROJECT
	require_once __DIR__ . "/../src/CodePool/_DEFINITIONS_.php";

	//CREATE A POINTER TO ENTITIES PATH - POINTER MUST BE AN ARRAY CONTAINING FULL PATH NAMES
	$paths 		= array( realpath(__DIR__ . "/../CodePool/Entity"));
	
	$dEnv       = new Dotenv();
	$arrEnv     = $dEnv->parse(file_get_contents(__DIR__ . "/../.env"), __DIR__ . "/../.env");
	$dbDriver   = null;
	if(isset($arrEnv['DATABASE_URL'])){
		$arrConnData    = array_values(array_filter(preg_split("#:\/\/|:|\/|@{1}#si",$arrEnv['DATABASE_URL'])));
		$dbDriver       = "pdo_{$arrConnData[0]}";
		// MYSQL CONFIGURATION
		if(stristr($arrEnv['DATABASE_URL'], 'mysql')){
			$dbConn     = array(
				'driver'    => $dbDriver,
				'user'      => $arrConnData[1],
				'password'  => $arrConnData[2],
				'host'      => $arrConnData[3],
				'port'  	=> $arrConnData[4],
				'dbname' 	=> $arrConnData[5],
			);
		}else if(stristr($arrEnv['DATABASE_URL'], 'sqlite')){
			// SQL LITE CONFIGURATION
			if(stristr($arrConnData[1], "kernel.")){
				$kernel     = new \App\Kernel('dev', true);
				$kernelPath = str_replace( "kernel.", "", str_replace("%", "", $arrConnData[1]));
				$basePath   = realpath($kernelPath);
				
				switch($kernelPath){
					case 'project_dir':
						$basePath = $kernel->getProjectDir();
						break;
					case 'log_dir':
						$basePath = $kernel->getLogDir();
						break;
					case 'cache_dir':
						$basePath = $kernel->getCacheDir();
						break;
				}
			}
			$dbConn     = array(
				'driver'    => $dbDriver,
				'path'      => "{$basePath}/{$arrConnData[2]}/{$arrConnData[3]}",
			);
			
		}
	}
	
	//CREATE A SIMPLE "DEFAULT" DOCTRINE ORM CONFIGURATION FOR ANNOTATIONS
	$isDevMode 	= true;
	$config 	= Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
	
	$cache  = new ArrayCache();
	$reader = new AnnotationReader();
	$driver = new AnnotationDriver($reader, $paths);
	$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

	$config->setMetadataCacheImpl( $cache );
	$config->setQueryCacheImpl( $cache );
	$config->setMetadataDriverImpl( $driver );
	
	

	class E_MAN{
		/**
		 * @var Doctrine\ORM\EntityManager
		 */
		protected static $entityManager;

		/**
		 * @return mixed
		 */
		public static function getEntityManager(){
			return self::$entityManager;
		}

		/**
		 * @param Doctrine\ORM\EntityManager $entityManager
		 */
		public static function setEntityManager(Doctrine\ORM\EntityManager $entityManager){
			self::$entityManager = $entityManager;
		}
	}
	
	// INSTANTIATE THE ENTITY MANAGER
	$entityManager  = EntityManager::create($dbConn,  $config);


	// - ADD SUPPORT FOR MYSQL ENUM-TYPES....
	if($dbDriver == 'pdo_mysql'){
		$platform = $entityManager->getConnection()->getDatabasePlatform();
		$platform->registerDoctrineTypeMapping('enum', 'string');
	}

	// STATICALLY SET THE ENTITY MANAGER SO IT CAN BE ACCESSED BY CLASSES NEEDING IT....
	E_MAN::setEntityManager($entityManager);
	
