<?php

require_once('Handlers/BaseStorageHandler.php');

use NxSys\Toolkits\OpenVFS\Handlers\BaseStorageHandler;

/**
 * @author feamsr00
 *
 */
abstract class CombinedStorageHandler extends BaseStorageHandler
{
	protected $hfRetrievalStrategyCallback;
	protected $hfStorageStrategryCallback;

	protected abstract function setRetrievalStrategy(callable $hfStrategy);

	protected abstract function setStorageStrategy(callable $hfStrategy);
}
