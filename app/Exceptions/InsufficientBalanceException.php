<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InsufficientBalanceException extends HttpException{
    public function __construct(){
        parent::__construct(422, 'Kassada yetarli mablag‘ mavjud emas');
    }
}
