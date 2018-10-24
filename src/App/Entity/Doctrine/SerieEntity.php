<?php
namespace App\Entity\Doctrine;

use App\Twig\TwigExtensions;
use Core\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class SerieEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var DepotEntity
     */
    protected $depot;

    /**
     * @var null|string
     */
    protected $label = null;

    /**
     * @var null|SerieStarterEntity
     */
    protected $starter;

    /**
     * @var PlanningEntity[]
     */
    protected $plannings;

    /**
     * @var array
     */
    protected $excluded = ['starter'];

    /**
     * @return string
     */
    public function __toString()
    {
        return TwigExtensions::serieLabelFilter($this, true);
    }

    /**
     * SerieEntity constructor.
     */
    public function __construct()
    {
        $this->plannings = new ArrayCollection();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('id', [
            new Assert\Range([
                'min' => 100,
                'max' => 999,
                'invalidMessage' => "La valeur saisie n'est pas un identifiant de Gestionnaire valide",
                'minMessage' => "L'identifiant du Gestionnaire doit être de {{ limit }} ou plus",
                'maxMessage' => "L'identifiant du Gestionnaire ne peut pas dépasser {{ limit }}"
            ])
        ]);

        $metadata->addPropertyConstraints('label', [
            new Assert\Length([
                'min' => 1,
                'max' => 16,
                'minMessage' => "Le nom du Gestionnaire doit au mois faire {{ limit }} caractères",
                'maxMessage' => "Le nom du Gestionnaire ne peut dépasser {{ limit }} caractères"
            ])
        ]);
    }

    /**
     * @return array
     */
    public function getTips(): array
    {
        if ($this->getPlannings()->isEmpty()) {
            $this->addTip('danger', "Attaché à aucun planning");
        } else {
            $count = $this->getPlannings()->count();
            $plural = ($count > 1) ? "s" : null;

            $this->addTip('info', "Visible dans {$count} planning{$plural}");
        }

        return parent::getTips();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return SerieEntity
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DepotEntity
     */
    public function getDepot(): DepotEntity
    {
        return $this->depot;
    }

    /**
     * @param DepotEntity $depot
     * @return SerieEntity
     */
    public function setDepot(DepotEntity $depot): self
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return SerieEntity
     */
    public function setLabel(?string $label): self
    {
        if (isset($label) && !empty($label)) {
            $this->label = strtoupper(strip_tags($label));
        } else {
            $this->label = null;
        }

        return $this;
    }

    /**
     * @return SerieStarterEntity|null
     */
    public function getStarter(): ?SerieStarterEntity
    {
        return $this->starter;
    }

    /**
     * @return SerieStarterEntity|null
     */
    public function getFirstStarter(): SerieStarterEntity
    {
        if ($this->starter instanceof SerieStarterEntity === false) {
            $this->addStarter(
                (new SerieStarterEntity)
                    ->setSerie($this)
                    ->setStarter(($this->getId() * 1000) + 1)
                    ->setCreatedAt(new \DateTime())
            );
        }

        return $this->getStarter();
    }

    /**
     * @param SerieStarterEntity $starterEntity
     */
    public function addStarter(SerieStarterEntity $starterEntity)
    {
        $this->starter = $starterEntity;
    }

    /**
     * @return PlanningEntity[]
     */
    public function getPlannings()
    {
        return $this->plannings;
    }
}
