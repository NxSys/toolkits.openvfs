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

class MyStorageHandler extends BaseStorageHandler
{
	public $sSchemeName='MySto';
	public $sStreamWrapperClass='MyStorageStreamWrapper';

	public function getSchemeName()
	{
		return $this->sSchemeName;
	}

	public function getStreamWrapperName()
	{
		return 'F2Dev\Utils\OpenVFS\Wrappers\\'.$this->sStreamWrapperClass;
	}
}
