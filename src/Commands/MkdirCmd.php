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

//Local Namespace
namespace NxSys\Toolkits\OpenVFS\Commands;

	//Framework Namespaces
use Symfony\Component\Console as SfConsole;

	//3rdParty Namespaces
use Monolog\Logger;

class MkdirCmd extends BaseCommand
{
	/**
	 * function configure
	 *
	 * @return
	 */
	public function configure()
	{
		$this
			->setName('mkdir')
			->setDescription('Creates Directories')
			->setHelp('Foo');
	}

}