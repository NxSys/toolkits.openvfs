<?php

namespace NxSys\Toolkits\OpenVFS;
use NxSys\Toolkits\OpenVFS\ExceptionType;

/**
 * The point of this is to preserve/integrate the class type hireacrhy since
 * SPL already has an exception class hireacrhy.
 *
 *
 */
class StorageHandlerNotFoundException extends \RuntimeException implements ExceptionType
{}