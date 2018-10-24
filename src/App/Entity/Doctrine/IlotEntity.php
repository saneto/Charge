<?php
namespace App\Entity\Doctrine;

use App\Utils\HexaColor;
use Core\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class IlotEntity extends Entity
{
    protected $excluded = ['plannings'];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var PlanningEntity[]
     */
    protected $plannings;

    /**
     * @var SerieEntity
     */
    // protected $serie;

    /**
     * @var DepotEntity
     */
    protected $location;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var null|int
     */
    protected $dx;

    /**
     * @var null|int
     */
    protected $dy;

    /**
     * @var null|int
     */
    protected $dz;

    /**
     * @return string
     */
    public function __toString()
    {
        $label = $this->getDimensions() ?? $this->getLabel();
        return $this->getName() . " ({$label})";
    }

    /**
     * IlotEntity constructor.
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
        $metadata->addPropertyConstraints('name', [
            new Assert\NotBlank([
                'message' => "Le nom de l'îlot est obligatoire"
            ]),
            new Assert\Length([
                'min' => 5,
                'max' => 16,
                'minMessage' => "Le nom de l'îlot doit être de {{ limit }} caractères minimum",
                'maxMessage' => "Le nom de l'îlot doit être de {{ limit }} caractères maximum",
            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTips(): array
    {
        if ($this->getPlannings()->isEmpty()) {
            $this->addTip('warning', "Cet îlot est orphelin");
        }
        if ($this->hasDimensions() === false) {
            $this->addTip('warning', "Pas de dimensions renseignées");
        } else {
            $this->addTip('info', "Dimensions: " . $this->getDimensions());
        }

        return parent::getTips();
    }

    /**
     * @param int $id
     *
     * @return IlotEntity
     */
    public function setId(int $id): IlotEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return IlotEntity
     */
    public function setName(string $name): IlotEntity
    {
        $this->name = strtoupper($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|PlanningEntity[]|null
     */
    public function getPlannings()
    {
        return $this->plannings;
    }

    /**
     * @param PlanningEntity $newPlanning
     * @return IlotEntity
     */
    public function attachPlanning(PlanningEntity $newPlanning): IlotEntity
    {
        $ids = [];
        foreach ($this->plannings as $planning) {
            $ids[] = $planning->getId();
        }

        if (in_array($newPlanning->getId(), $ids) === false) {
            $this->plannings->add($newPlanning);
        }

        return $this;
    }

    /**
     * @param PlanningEntity $oldPlanning
     * @return IlotEntity
     */
    public function detachPlanning(PlanningEntity $oldPlanning): IlotEntity
    {
        foreach ($this->plannings as $k => $planning) {
            if ($oldPlanning->getId() === $planning->getId()) {
                $this->plannings->remove($k);
            }
        }

        return $this;
    }

    /**
     * @param SerieEntity $serie
     *
     * @return IlotEntity
     */
    /*public function setSerie(SerieEntity $serie): IlotEntity
    {
        $this->serie = $serie;

        return $this;
    }*/

    /**
     * @return SerieEntity
     */
    /*public function getSerie(): SerieEntity
    {
        return $this->serie;
    }*/

    /**
     * @param DepotEntity $depot
     *
     * @return IlotEntity
     */
    public function setLocation(DepotEntity $depot)
    {
        $this->location = $depot;

        return $this;
    }

    /**
     * @return DepotEntity
     */
    public function getLocation(): DepotEntity
    {
        return $this->location;
    }

    /**
     * @param string $label
     *
     * @return IlotEntity
     */
    public function setLabel(string $label): IlotEntity
    {
        if (isset($label) && !empty($label)) {
            $this->label = $label;
        } else {
            $this->label = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $color
     *
     * @return IlotEntity
     */
    public function setColor(string $color): IlotEntity
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return HexaColor
     */
    public function getColor(): HexaColor
    {
        if($this->color instanceof HexaColor === false) {
            $this->color = (new HexaColor())->setColor($this->color);
        }

        return $this->color;
    }

    /**
     * @return int|null
     */
    public function getDx(): ?int
    {
        return $this->dx ?? 0;
    }

    /**
     * @param int|null $dx
     * @return IlotEntity
     */
    public function setDx(?int $dx): IlotEntity
    {
        if ($dx < 1) {
            $dx = null;
        }

        $this->dx = $dx;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDy(): ?int
    {
        return $this->dy ?? 0;
    }

    /**
     * @param int|null $dy
     * @return IlotEntity
     */
    public function setDy(?int $dy): IlotEntity
    {
        if ($dy < 1) {
            $dy = null;
        }

        $this->dy = $dy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDz(): ?int
    {
        return $this->dz ?? 0;
    }

    /**
     * @param int|null $dz
     * @return IlotEntity
     */
    public function setDz(?int $dz): IlotEntity
    {
        if ($dz < 1) {
            $dz = null;
        }

        $this->dz = $dz;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasDimensions(): bool
    {
        return $this->getDx() + $this->getDy() + $this->getDz() > 1;
    }

    /**
     * @return string|null
     */
    public function getDimensions(): ?string
    {
        if ($this->hasDimensions()) {
            $dim = "{$this->getDx()}x{$this->getDy()}";
            if ($this->getDz() >= 1) {
                $dim .= "x{$this->getDz()}";
            }

            return $dim;
        }

        return null;
    }

    /**
     * @param array $dimensions
     */
    public function setDimensions(array $dimensions): void
    {
        foreach ($dimensions as $k => $dimension) {
            if (!is_numeric($dimension)) {
                $dimension = 0;
            }

            switch ($k) {
                case 'x':
                    $this->setDx($dimension);
                    break;
                case 'y':
                    $this->setDy($dimension);
                    break;
                case 'z':
                    $this->setDz($dimension);
                    break;
            }
        }
    }
}
