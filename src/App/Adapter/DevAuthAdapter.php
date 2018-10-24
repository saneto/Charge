<?php
namespace App\Adapter;

use App\Entity\Dev;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class DevAuthAdapter implements AdapterInterface
{
    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $identity = (new Dev\GaetanSimonEntity());

        return new Result(Result::SUCCESS, $identity);
    }
}
