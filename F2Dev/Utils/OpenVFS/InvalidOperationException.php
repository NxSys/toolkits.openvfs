<?php

namespace F2Dev\Utils\OpenVFS;
use F2Dev\Utils\OpenVFS\ExceptionType;

/**
 * The point of this is to preserve/integrate the class type hireacrhy since
 * SPL already has an exception class hireacrhy.
 *
 *
 */
class InvalidOperationException extends \RuntimeException implements ExceptionType
{}