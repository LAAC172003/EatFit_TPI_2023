<?php

namespace Eatfit\Site\Core\Exception;



class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
}