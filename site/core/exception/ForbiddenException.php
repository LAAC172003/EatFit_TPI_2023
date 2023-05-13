<?php

namespace Eatfit\Site\Core\Exception;


class ForbiddenException extends \Exception
{
    protected $message = "Vous n'avez pas la permission d'accéder à cette page";
    protected $code = 403;
}