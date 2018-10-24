<?php
namespace App\Entity\Doctrine\Superclass;

use App\Entity\Dev\GaetanSimonEntity;
use App\Provider;
use Core\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Zend\Permissions\Acl\Role\RoleInterface;

abstract class SuperclassUserEntity extends Entity implements RoleInterface
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var null|string
     */
    protected $vme_id;

    /**
     * @var null|string
     */
    protected $displayname;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var string
     */
    protected $avatar;

    /**
     * @var array
     */
    protected $excluded = ['role'];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDisplayname();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('vme_id', [
            new Assert\Length([
                'min' => 2,
                'max' => 2,
                'exactMessage' => "Le Code Vendeur doit faire {{ limit }} caractères"
            ])
        ]);

        $metadata->addConstraint(new Assert\Callback('validateRole'));
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     *
     * @return bool
     */
    public function validateRole(ExecutionContextInterface $context, $payload)
    {
        if ($this instanceof GaetanSimonEntity) {
            return true;
        }

        if ($this->getRole() === null || empty($this->getRole())) {
            $context->buildViolation("Vous devez préciser un rôle")
                ->atPath('role')
                ->addViolation();
        } elseif (in_array($this->getRole(), Provider\AclProvider::getRoles()) === false) {
            $inputRole = $this->getRole();
            $allowedRoles = implode(', ', Provider\AclProvider::getRoles());

            $context->buildViolation("Le rôle \"{$inputRole}\" n'est pas valide (autorisés: {$allowedRoles})")
                ->atPath('role')
                ->addViolation();
        }
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getVmeId(): ?string
    {
        return $this->vme_id;
    }

    /**
     * @param string $vme_id
     * @return self
     */
    public function setVmeId(string $vme_id): self
    {
        $this->vme_id = strtoupper($vme_id);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    /**
     * @param string $displayname
     * @return self
     */
    public function setDisplayname(string $displayname): self
    {
        $this->displayname = $displayname;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole(string $role)
    {
        $this->role = strtolower($role);
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->getRole() . '_' . $this->getId();
    }
}