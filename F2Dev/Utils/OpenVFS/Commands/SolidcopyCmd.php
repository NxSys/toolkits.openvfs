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
namespace F2Dev\Utils\OpenVFS\Commands;

use F2Dev\Utils\OpenVFS;

	//Framework Namespaces
use Symfony\Component\Console as SfConsole,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputOption;

	//3rdParty Namespaces
use Monolog\Logger;

//Resources
define('HELP_SOLIDCOPY', <<< EOD
Hello world
EOD
);

class SolidcopyCmd extends BaseCommand
{
	const RET_NOERROR=	 0;
	const RET_BADINPUT= 10;
	const RET_MISCERR= 255;


	/**
	 * @var bool
	 */
	public $bLoggingEnabled=false;

	/**
	 * @var \SplFileObject
	 */
	public $hLogFile;

	/**
	 * function configure
	 *
	 * @return
	 */
	public function configure()
	{
		$this
			->setName('solidcopy')
			->setDescription('Reliable Filecopy Tool')
			->setHelp(HELP_SOLIDCOPY)
			//->setDefinition()
			->addArgument('session', InputArgument::OPTIONAL,
						  'Name of session data file')
			->addArgument('source', InputArgument::OPTIONAL,
						  'File\URL of Source File\Dir')
			->addArgument('dest', InputArgument::OPTIONAL,
						  'File\URL of Destination File\Dir')
			//Options
			->addOption('no-clober', 'nc', InputOption::VALUE_NONE,
						'Do not overwrite files in destination.')
			->addOption('block-size', null, InputOption::VALUE_REQUIRED,
						'Copy data in X size chunks.', 1024)
			->addOption('src-retry', null, InputOption::VALUE_REQUIRED,
						'Number of times to try copying from src before skipping the file', 5)
			->addOption('dest-retry', null, InputOption::VALUE_REQUIRED,
						'Option')
			//->addOption('no-src-dupes', null, InputOption::VALUE_NONE,
			//			'When detected, duplicates are cached and are not re-transfered from the source.')
			//->addOption('no-dest-dupes', null, InputOption::VALUE_NONE,
			//			'When detected, duplicates are skipped and are not re-transfered to the destination.')
			->addOption('x', null, InputOption::VALUE_NONE,
						'Option')
			->addOption('save-session', null, InputOption::VALUE_NONE,
						'Do not delete session file on completion.')
			->addOption('progress', 'p', InputOption::VALUE_OPTIONAL,
						"May be 'bar' or 'dot'.")

			->addOption('log', null, InputOption::VALUE_REQUIRED,
						'File to log output to.')

			// OVFS Options
			->addOption('src-handler', null, InputOption::VALUE_REQUIRED,
						'Named OpenVFS storage handler to load for source path.')
			->addOption('src-handler-opt', 'sopt', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
						'Options to use for source storage handler (e.g. "a=b"). See handler documentation.')
			->addOption('dest-handler', null, InputOption::VALUE_REQUIRED,
						'Named OpenVFS storage handler to load for destination path.')
			->addOption('dest-handler-opt', 'dopt', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
						'Options to use for destination storage handler (e.g. "a=b"). See handler documentation for details.')
			;
		return;
	}

	public function execute(SfConsole\Input\Input $oIn,
							SfConsole\Output\Output $oPut)
	{
		//verify logic of parameters
		if(!$this->verifyInputSanity($oIn))
		{
			$oPut->writeln($this->asText());
			$oPut->writeln('There was an error in your parameters. Please check the documentation and try again.');
			return self::RET_BADINPUT;
		}

		//Initialize
		$sSourcePath=$oIn->getArgument('source');
		$sDestinationPath=$oIn->getArgument('dest');
		$this->oPut=$oPut;

		//@todo setup error handlers?

		//get OpenVFS loaded
		new OpenVFS\Manager;
		if(!class_exists('F2Dev\Utils\OpenVFS\Manager'))
		{
			$oPut->writeln('OpenVFS can not be found. Please verify the requirements in the documentation.');
			return self::RET_MISCERR;
		}

		if($sLogFile=$oIn->getOption('log'))
		{
			//begin logging $oPut
			try
			{
				$this->setupLogger($sLogFile); //if no...
			}
			catch(\RuntimeException $e)
			{
				$oPut->writeln('Unable to open logfile.');
				return self::RET_MISCERR;
			}
			$this->log(sprintf('%s Started',
							   $this->getName()));
		}

		//if using a session file, save/merge input defs

		//setup session

			//setup environment

		try
		{
			$this->out('Attempting to access source...');
			$hSource=OpenVFS\Manager::connect($sSourcePath);

			$this->out('Attempting to access destination...');
			$hDest=OpenVFS\Manager::connect($sDestinationPath);
		}
		//catch(OpenVFS\Exception $e) {}
		catch(\UnexpectedValueException $e)
		{
			//for common errors
			// faulting call, message, code
			list( , , $iCode)=sscanf($e->getMessage(), '%s %[^(] (code: %i)');

			// if we got an error about remote hosts
			if(   1 == substr_count($e->getMessage(), 'remote host file access not supported')
			   //aaand if we were passed a file:// url
			   && (   1 == substr_count($sSourcePath, 'file://')
				   || 1 == substr_count($sDestinationPath, 'file://')) //in either path
			  )
			{
				//then congratulations you stumbled on to random lame error buried in php's
				//stream handling somewhere in the bowls of streams.c that ONLY affects file://

				//but because I'm a nice guy i won't confuse you
				$oPut->writeln("<error>file:// paths *must* come in the form of file://c:/my/dir "
							   ."or file:///home/my/dir.</error>");
				$oPut->writeln('Please verify this format for a local path or use an appropriate remote scheme');
				return self::RET_BADINPUT;
			}
			elseif(  3 == $iCode)
			{
				$this->out(' Unable to continue: The system cannot find the path specified.');
				return self::RET_MISCERR;
			}
			elseif(123 == $iCode)
			{
				$this->out(' Unable to continue: The filename, directory name, or volume label syntax is incorrect.');
				$this->out('  Please check that path exists and format is correct.');
				return self::RET_MISCERR;
			}
			else
			{
				//ummm... poop?
				throw new InvalidOperationException($e->getMessage(), null, $e);
			}
		}

		$this->out('Sucessfully opened endpoints.');

		//start operation
		$hSourceItr=new \RecursiveIteratorIterator($hSource);
		foreach($hSourceItr as $oSrcItem)
		{
			//if curr path in skip list, skip
			$oPut->writeln((string)$oSrcItem);
			$this->fileCopyTransaction($oSrcItem, $hDest);
		}

		return 0;
	}

	protected function verifyInputSanity(SfConsole\Input\InputInterface $oIn)
	{
		//test and echo
		return true;
	}

	protected function fileCopyTransaction(FilesystemIterator $hSrc,
										   FilesystemIterator $hDest)
	{
		;
	}

	protected function recordTransaction()
	{
		//record
	}

	public function out($sLine)
	{
		$this->oPut->writeln($sLine);
		if($this->bLoggingEnabled)
		{
			//write to log
			$this->log($sLine);
		}
		return;
	}

	/**
	 * @throws \RuntimeException when unable to open file
	 */
	protected function setupLogger($sFile)
	{
		$hLog=new \SplFileObject($sFile,'a+');
		$this->bLoggingEnabled=true;
		$this->hLogFile=$hLog;
		return true;
	}

	/**
	 *
	 */
	public function log($sLine)
	{
		//do logging
		$sFmtStr='[%s] %s';
		$this->hLogFile->fwrite(sprintf($sFmtStr, date('c'), $sLine));
		$this->hLogFile->fflush();
		return;
	}
}

interface SolidcopyExceptionType
{}

class InvalidOperationException extends \RuntimeException implements SolidcopyExceptionType
{}