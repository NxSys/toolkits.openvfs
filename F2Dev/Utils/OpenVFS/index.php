<?php
/**
 *
 */

//namespace F2Dev\Utils\DSVFS;

require_once 'Symfony/Component/ClassLoader/UniversalClassLoader.php';
$oUCL=new Symfony\Component\ClassLoader\UniversalClassLoader();
$oUCL->registerNamespace('F2Dev\Utils\OpenVFS', '\dev\prjs\F2Dev\Utils\OpenVFS');
$oUCL->register();


use F2Dev\Utils\OpenVFS;
use F2Dev\Util\OpenVFS\Manager;
new OpenVFS\Manager();


echo "testing....";
$aVfsAccessOps= [
		"identifier" => "flynn@encom.com",
		"credential" => "biodigitaljazz"
];

$aVfsHandlerOps= [
	'prefix' => 'msto',
	'default_mode' => OpenVFS\MODE_READ | OpenVFS\MODE_WRITE,
	'FileSystemWrapper' => 'Handlers\RACS\FilesystemIteratorAdapter',
	'FileSystemFlags' => \FilesystemIterator::CURRENT_AS_FILEINFO,
	'mount_point' => 'apps', //simply a path prefix
	'test_on_fail' => false
];

$oHandler=new OpenVFS\Handlers\MyStorageHandler($aVfsHandlerOps);

//the storage provider may not support remote creation...
OpenVFS\Manager::createVFSContainer('My', 'locator',
									$aVfsHandlerOps);

//load but don't connect
OpenVFS\Manager::loadStorageHandler('My', $aVfsHandlerOps); //ret null
// registers the stream wrapper but doesn't connect to anything


OpenVFS\Manager::connect('mysto://identifier:credential:opts@foo/apps/bar/baz.txt');
//may throw 'can't find SH/SW for scheme mysto'


//sp requires options
OpenVFS\Manager::openVFS('My', 'locator', 'path',
						 $aVfsAccessOps, $aVfsHandlerOps); //ret FilesystemIterator
//similar to <MyStorage>://<locator>/<path>
//locator MAY be . for SHs with a universal endpoint, eg bigG, Dropbox, etc

//sp MAY require options
OpenVFS\Manager::openVFS($oHandler, 'locator', 'path'); //ret FilesystemIterator
