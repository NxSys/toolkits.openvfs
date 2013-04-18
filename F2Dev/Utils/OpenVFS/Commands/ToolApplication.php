<?php

namespace F2Dev\Utils\OpenVFS\Commands;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class ToolApplication extends Application
{
	protected $sCmdName;
	protected $oCmd;

	/**
	 * Constructor
     * @param string $name     The name of the application
     * @param string $version  The version of the application
     * @param string $sCmdName The command the application is based on
     * @param string Command   An instance of the command the application is based on
	 */
	public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN',
								$oCommandInstance)
	{
		$this->oCmd=$oCommandInstance;
		$this->sCmdName=$oCommandInstance->getName();
		parent::__construct($name, $version);
	}

    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        return $this->oCmd->getName();
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = $this->oCmd;

        return $defaultCommands;
    }

	/**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}