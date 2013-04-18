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
//is procedural

use F2Dev\Utils\OpenVFS;

	//Framework Namespaces
use Symfony\Component\Console as SfConsole;

	//3rdParty Namespaces
use Monolog\Logger;

//You can move this directory or even just this file, but you must ensure that
//OpenVFS files are available. We can use the default autoloader but must insure
//that the containing folder (eg vendors, src, etc) of F2Dev\* is on the path
// Please note this also depends on SfCompoents\Console

$sPATH_TO_VENDOR='c:\dev\prjs';

if(!class_exists('OpenVFS\Manager'))
{
	//add src to include path
	set_include_path($sPATH_TO_VENDOR.PATH_SEPARATOR.get_include_path());

	//init autoload with system default
	spl_autoload_register();
}

new OpenVFS\Manager;

$oFsApp=new SfConsole\Application('OpenVFS Utility',OpenVFS\Version);
$oFsApp->addCommands(
	[
		new OpenVFS\Commands\SolidcopyCmd,
		new OpenVFS\Commands\MkdirCmd
	]
);

$oShell=new SfConsole\Shell($oFsApp);
$oShell->run();
