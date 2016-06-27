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

//Please make sure that the F2Dev\Utils\OpenVFS path is available for autoloading
use F2Dev\Utils\OpenVFS;
use Symfony\Component\Console as SfConsole;

//You can move this directory or even just this file, but you must ensure that
//OpenVFS files are available. We can use the default autoloader but you must insure
//that the containing folder (e.g. vendors, src, etc) of F2Dev\* is on the path
// Please note this also depends on SfCompoents\Console

$sPATH_TO_VENDOR='c:\dev\prjs';

if(!class_exists('OpenVFS\Manager'))
{
	//add src to include path
	set_include_path($sPATH_TO_VENDOR.PATH_SEPARATOR.get_include_path());

	//init autoload with system default
	spl_autoload_register();
}

$oApp=new \ToolApplication('Solidcopy','1.0a',
						   new OpenVFS\Commands\SolidcopyCmd);
$oApp->setCatchExceptions(false);
$oApp->run();