<?php
namespace App\Adapter;

use App\Entity\Doctrine;
use App\Exception\CasAuthException;
use Doctrine\ORM;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class CasAuthAdapter implements AdapterInterface
{
    /**
     * @var int
     */
    const MISSING_VME_ID = 1001;

    /**
     * @var \phpCAS
     */
    private $phpCAS;

    /**
     * @var ORM\EntityManager
     */
    private $em;

    /**
     * AuthAdapter constructor.
     *
     * @param \phpCAS           $phpCAS
     * @param ORM\EntityManager $em
     */
    public function __construct(\phpCAS $phpCAS, ORM\EntityManager $em)
    {
        $this->phpCAS = $phpCAS;
        $this->em = $em;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     *
     * @throws CasAuthException
     */
    public function authenticate()
    {
        $phpCAS = $this->phpCAS;

        // redirection vers la page de connexion si non authentifié
        if ($phpCAS::isAuthenticated()) {
            /**
             * @var Doctrine\UserEntity $identity
             */
            $identity = $this->em->getRepository(Doctrine\UserEntity::class)->find($phpCAS::getUser());

            if ($identity instanceof Doctrine\UserEntity === false) {
                $identity = (new Doctrine\UserEntity())
                    ->setId($phpCAS::getUser())
                    ->setDisplayname($phpCAS::getAttribute('displayName'));

                $this->em->persist($identity);
                $this->em->flush();

                $code = static::MISSING_VME_ID;
            } elseif ($identity->getVmeId() === null) {
                $code = static::MISSING_VME_ID;
            } else {
                $code = Result::SUCCESS;
            }

            return new Result($code, $identity);
        }

        throw new CasAuthException("No ticket, redirecting to {$phpCAS::getServerLoginURL()}");
    }
}
