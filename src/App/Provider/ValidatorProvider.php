<?php
namespace App\Provider;

use Core\Provider\ProviderFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorProvider extends ProviderFactory
{
    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): ValidatorInterface {
            return Validation::createValidatorBuilder()
                ->addMethodMapping('loadValidatorMetadata')
                ->getValidator();
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'validator';
    }
}
