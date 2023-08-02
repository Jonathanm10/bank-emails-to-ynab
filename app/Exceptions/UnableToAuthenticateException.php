<?php

namespace App\Exceptions;

use Exception;

class UnableToAuthenticateException extends Exception
{
   public static function because(string $reason): static
   {
         return new static($reason);
   }
}
