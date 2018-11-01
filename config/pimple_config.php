<?php

require_once __DIR__ . "/bootstrap.php";
require_once __DIR__ . "/../vendor/autoload.php";
	
use App\CodePool\Entity\SoundSpace;
use App\CodePool\Helpers\Builders\Weavers\HTMLElementWeaver;
use Pimple\Container;

$container = new Container();

$services = [
	'CodePool\Base\Poiz\Forms\FormBaker'            => ['alias'=>'FB',                  'params'=>[]],
	'CodePool\Base\Poiz\Forms\Validator'            => ['alias'=>'Validator',           'params'=>[]],
	'CodePool\Base\Poiz\Forms\ErrorLogger'          => ['alias'=>'ErrorLogger',         'params'=>[]],
	'CodePool\DataObjects\PzCsvFileEntity'          => ['alias'=>'PzCsvFileEntity',     'params'=>[]],
	'CodePool\DataObjects\Repo\PzCsvFileEntityRepo' => ['alias'=>'PzCsvFileEntityRepo', 'params'=>[]],
	'CodePool\FormObjects\PZCSVFileForm'            => ['alias'=>'PZCSVFileForm',       'params'=>[]],
	'CodePool\FormObjects\PZCSVRenderForm'          => ['alias'=>'PZCSVRenderForm',     'params'=>[]],
	'CodePool\Poiz\Bridges\PoizPluginHelper'        => ['alias'=>'PoizPluginHelper',    'params'=>[]],
	'CodePool\Poiz\Bridges\Octopus'                 => ['alias'=>'Octopus',             'params'=>[]],
];

foreach($services as $fullClassName=>$arrServiceData){
	$container[$arrServiceData['alias']] = function ($c) use($fullClassName){
		return new $fullClassName();
	};
}

$entityConfig   = array(
	'fileName'      => CODE_POOL . '/DataObjects/Sampler.php',
	'tableName'     =>'sample_tbl',
	'className'     =>'Sampler',
	'nameSpace'     =>'CodePool\DataObjects',
	'usableClasses' =>array(
		'Doctrine\ORM\Mapping as ORM',
		'Doctrine\ORM\Mapping\Id',
		'Doctrine\ORM\Mapping\Table',
		'Doctrine\ORM\Mapping\Column',
		'Doctrine\ORM\Mapping\Entity',
		'Doctrine\ORM\Mapping\OneToOne',
		'Doctrine\ORM\Mapping\JoinColumn',
		'Doctrine\ORM\Mapping\GeneratedValue',
		'Doctrine\ORM\Mapping\JoinColumns',
		'Doctrine\ORM\Mapping\OneToMany',
		'Doctrine\ORM\Mapping\ManyToOne',
		'Doctrine\ORM\Mapping\ManyToMany',
	),
);

$container['entityConfig'] = function ($c) use($entityConfig) {
	return $entityConfig;
};

$container['eMan'] = function ($c){
	return E_MAN::getEntityManager();
};

$container['Doctrine.ORM.EntityManager'] = function ($c) {
	return E_MAN::getEntityManager();
};

$container['weaver'] = function ($c) {
	return new HTMLElementWeaver();
};

$container['E_MAN'] = function ($c) {
	return $GLOBALS['E_MAN'];
};

$container['SoundSpace'] = $container->factory(function ($c) {
	$conn   = $c['E_MAN']->getConnection();
	$dql    = $c['E_MAN']->createQueryBuilder();
	return new SoundSpace($c['E_MAN'], $dql, $conn, $c);
});

$GLOBALS['container']   = $container;
$GLOBALS['E_MAN']       = $container['Doctrine.ORM.EntityManager'];
