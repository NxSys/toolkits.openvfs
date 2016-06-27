<?php
/**
 * StorageHandler For the file:// stream
 * $Id$
 *
 * DESCRIPTION
 *  Desc
 *
 *
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

namespace NxSys\Toolkits\OpenVFS\Handlers;

/**
 *
 * Note: when using the file:// url, it must have the mountpoint specified as / or X:
 *
 */
class LocalFileStorageHandler extends BaseStorageHandler
{
	public $sSchemeName='file';
	//public $sStreamWrapperClass='MyStorageStreamWrapper';

	public function getSchemeName()
	{
		return $this->sSchemeName;
	}

	public function getStreamWrapperName()
	{
		return 'BUILT-IN_'.$this->sSchemeName;
	}

	/**
	 * file:// cares not for authorities
	 */
	public function getStreamPath($sAuthority)
	{
		// file:// cares not for authorities
		$sPath=sprintf('%s:/', $this->getSchemeName());
		return $sPath;
	}

	public final function register(array $aHandlerOptions=null)
	{
		//nothing to register as this is built in
		if(!in_array($this->sSchemeName, stream_get_wrappers()))
		{
			//but if unregistered, reregister
			stream_wrapper_restore($this->getSchemeName());
		}
		return true;
	}
}
