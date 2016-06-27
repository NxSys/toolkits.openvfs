<?php
namespace F2Dev\Utils\OpenVFS;

// OpenVFS Version
const Version= '1.0-alpha';


//
//
//
const MODE_READ=		0b00001;
const MODE_WRITE=		0b00010; //implies create... unless NOCLOBER
const MODE_TRUNCATE=	0b00100;
const MODE_PTR_START=	0b00000; //always unless...
const MODE_PTR_END=		0b01000;
const MODE_NOCLOBER=	0b10000; //do not create if not exists, do not truncate if WRITE|PTR_START

//shorts
const MODE_APPEND=		0b01010;


/**
 * @author feamsr00
 *
 */
class Manager
{
	protected static $aLoadedHadlers=array();
	protected static $aRegisteredHadlers
		= [
			'file' => 'LocalFile'
		  ];


	/**
	 *
	 */
	function __construct()
	{
		//TODO - Insert your code here
		// preload exsisting builtin streams
	}

	/**
	 * Finds a storage handler
	 *
	 * @param \F2Dev\Utils\OpenVFS\Handlers\BaseStorageHandler|multitype $oStorageHandler
	 * @param array $aStorageHandlerOptions
	 * @return \F2Dev\Utils\OpenVFS\Handlers\BaseStorageHandler|multitype:
	 */
	static function loadStorageHandler($oStorageHandler, array $aStorageHandlerOptions)
	{
		/* @var $oStorageHandler Handlers\BaseStorageHandler */
		if ($oStorageHandler instanceof Handlers\StorageHandlerType)
		{
			$oStorageHandler->setConfig($aStorageHandlerOptions);
			$oStorageHandler->register();
			//buggy maybe?
			self::registerStoageHandler($oStorageHandler->getSchemeName(),
										str_replace('StorageHandler', '', get_class($oStorageHandler)));
			return $oStorageHandler;
		}

		//resolve $oStorageHandler
		if(!is_string($oStorageHandler))
		{
			throw new InvalidOperationException(
				'StorageHandler must be a valid name or StorageHandlerType, not '.gettype($StorageHandlerType));
		}
		$sNameofSH=sprintf('%s\Handlers\%sStorageHandler',
						   __NAMESPACE__, $oStorageHandler);
		if(!array_key_exists($sNameofSH, self::$aLoadedHadlers))
		{
			//autoload
			if(!class_exists($sNameofSH)) //implicit autoloading...
			{
				throw new StorageHandlerNotFoundException("The VFS handler: $sNameofSH, can not be found");
			}
			//however
			#soo.... class exists is broken? see: php bug#52339
			//if(class_exists($oStorageHandler))
			//{
			//	$sNameofSH=$oStorageHandler;
			//}
			//init handler
				//@todo see if o/w handlers is a bug....
			/** @var \F2Dev\Utils\OpenVFS\Handlers\BaseStorageHandler */
			$oStorageHandler=new $sNameofSH;
			$oStorageHandler->setConfig($aStorageHandlerOptions);
			$oStorageHandler->register();
			self::registerStoageHandler($oStorageHandler->getSchemeName(),
										$oStorageHandler);
			self::$aLoadedHadlers[$sNameofSH]=$oStorageHandler;
		}
		return self::$aLoadedHadlers[$sNameofSH];
	}

	/**
	 * This doesn't mean its loaded, just makes it easier to find
	 */
	static function registerStoageHandler($sHandlerSchemeName, $sHandlerName)
	{
		self::$aRegisteredHadlers[$sHandlerSchemeName]=$sHandlerName;
	}

	static function getStorageHandler($sIdentifier)
	{
		$aLoadedWrappers=stream_get_wrappers();
		//foreach ($aLoadedWrappers as $)
		//if $sId is in lw
			//if
	}

	static function createVFSContainer(){}

	/**
	 *
	 * Note: for file:// mount point must be '/' or 'X:' where x is a drive letter
	 *
	 * @param string|StorageHandlerType $oStorageHandler the SP name or SH class
	 * @param string $sLocatorString Also called the authority part. Some SHs may accept a '.'
	 * @param string $sPath should not contain trailing or leading slashes
	 * @param array $aAccessOpts
	 * @param array $aHandlerOpts
	 * @return \RecursiveDirectoryIterator
	 */
	static function openVFS($oStorageHandler, $sLocatorString, $sPath='/',
							array $aAccessOpts, array $aHandlerOpts)
	{
		$oStoHandler=self::loadStorageHandler($oStorageHandler, $aHandlerOpts);
		//$oSP=self::getStorageHandler($oStorageHandler);


		$sFsPath=sprintf('%s/%s/%s',
			$oStoHandler->getStreamPath($sLocatorString),
			array_key_exists('mount_point',$aHandlerOpts)?$aHandlerOpts['mount_point']:'.',
			$sPath?$sPath:null
		);
		var_dump($oStoHandler->getStreamPath($sLocatorString));
		var_dump(array_key_exists('mount_point',$aHandlerOpts)?$aHandlerOpts['mount_point']:'.');
		var_dump($sPath);
		//Undefined index: FileSystemFlags
		if(!array_key_exists('FileSystemFlags', $aHandlerOpts))
		{
			//maybeee?
			$aHandlerOpts['FileSystemFlags']=0;
		}
		echo $sFsPath.PHP_EOL;
		return new \RecursiveDirectoryIterator($sFsPath, $aHandlerOpts['FileSystemFlags']);
	}

	/**
	 * Loads a StorageHandler, if required, based on the scheme of the passed
	 * fully qualified path.
	 *
	 * @param $sFullyQualifiedPath
	 *
	 * @return RecursiveDirectoryIterator
	 */
	static function connect($sFullyQualifiedPath, $aOpts=[])
	{
		//see if this is already available
		if(@file_exists($sFullyQualifiedPath)) //we don't care about misc errors because this is only a shortcut
		{
			return new \RecursiveDirectoryIterator($sFullyQualifiedPath
									   /*, self::getModeChar($aOpts['default_mode']) */);
		}

		if(!is_array($aURIParts=parse_url($sFullyQualifiedPath)))
		{
			throw new \InvalidArgumentException('URI is malformed.');
		}
		if(!array_key_exists('scheme', $aURIParts))
		{
			throw new \InvalidArgumentException('Path is not fully qualified');
		}
		//any special handling or inline option detecting
		$aInlineOpts=[];
		//var_dump($aURIParts);
		switch($aURIParts['scheme'])
		{
			case 'file':
			{
				$sMountPoint=explode('/',$sFullyQualifiedPath)[2]; //yay hacks
				$aInlineOpts['mount_point']=$sMountPoint;
				break;
			}
		}
		//if(!)
		//specified options take priority over inline opts
		$aHandlerOpts=array_merge($aInlineOpts, $aOpts);

		//look for exsisting storage handler
		$sStorageHanlerName='LocalFile';

		//get 'path'
		$aPathParts=explode('/',$sFullyQualifiedPath);
		//the first 3 parts can go, then stringify
		$sBasePath=implode('/',array_slice($aPathParts, 2));
		//no /s required...
		$sBasePath=trim($sBasePath,'/\\');

		//get 'handle'
		self::openVFS($sStorageHanlerName, '.', $sBasePath, [], $aHandlerOpts);
	}

	/**
	 *
	 */
	function __destruct()
	{

		//TODO - Insert your code here
	}
}

?>