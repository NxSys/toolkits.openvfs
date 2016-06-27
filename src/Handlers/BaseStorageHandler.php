<?php
/**
 * FileHeadline
 * $Id$
 *
 * DESCRIPTION
 *  Desc
 *
 * @link https://f2dev.com/prjs/
 * @package NAME
 * @subpackage SUBNAME
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2013 F2 Developments, Inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

namespace F2Dev\Utils\OpenVFS\Handlers;

use F2Dev\Utils\OpenVFS;

abstract class BaseStorageHandler implements StorageHandlerType
{
	const name='vfs';


	public $aConfig=array();

	function __construct()
	{
		//brief setup, maybe
	}

	public function setConfig($aConfig)
	{
		$this->aConfig=$aConfig;
	}

	public function register(array $aHandlerOptions=null)
	{
		if(!$aHandlerOptions and !$this->aConfig)
		{
			throw new OpenVFS\InvalidOperationException('Register requires a passed or set configuration.');
		}
		(!$aHandlerOptions)?($aHandlerOptions=[]):null;
		$this->aConfig=array_merge_recursive($this->aConfig, $aHandlerOptions);

		$bIsUrl=0;
		if(isset($this->aConfig['StreamIsUrl']) && $this->aConfig['StreamIsUrl'] )
		{
			$bIsUrl=STREAM_IS_URL;
		}
		//register SW
		stream_wrapper_register($this->getSchemeName(),
								$this->getStreamWrapperName(),
								$bIsUrl);
		return;
	}

	/**
	 * function getStreamWrapperName
	 *
	 * @return
	 */
	abstract public function getStreamWrapperName();

	/**
	 * function getSchemeName
	 *
	 * @param $void
	 * @return
	 */
	abstract public function getSchemeName();

	/**
	 * function getStreamPath
	 * builds a url
	 * @param string $sAuthority the locator/authority part
	 * @return string
	 */
	public function getStreamPath($sAuthority)
	{
		//@todo build authority part
		if('.'==$sAuthority)
		{
			//why?
			$sAuthority='host.default';
		}
		$sPath=sprintf('%s://%s',
					   $this->getSchemeName(),
					   $sAuthority);
		return $sPath;
	}

	public function unregister()
	{
		stream_wrapper_unregister($this->getSchemeName());
	}

	function __destruct()
	{
		$this->unregister();
	}
}