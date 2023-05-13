<?php

namespace Eatfit\Site\Core\Exception;

class NotFoundException extends \Exception
{
    protected $message = 'Cette page n\'existe pas';
    protected $code = 404;
}