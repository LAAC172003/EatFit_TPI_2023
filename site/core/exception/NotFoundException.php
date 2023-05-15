<?php

namespace Eatfit\Site\Core\Exception;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Cette page n\'existe pas';
    protected $code = 404;
}