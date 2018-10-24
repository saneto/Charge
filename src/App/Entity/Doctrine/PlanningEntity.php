<?php
namespace App\Entity\Doctrine;

use Cocur\Slugify\Slugify;
use Core\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PlanningEntity extends Entity
{
    protected $excluded = ['ilots'];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var SerieEntity[]
     */
    protected $series;

    /**
     * @var ArrayCollection|IlotEntity[]
     */
    protected $ilots;

    /**
     * @var PlanningChargeEntity[]
     */
    protected $charges;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * PlanningEntity constructor.
     */
    public function __construct()
    {
        $this->series = new ArrayCollection();
        $this->ilots = new ArrayCollection();
        $this->charges = new ArrayCollection();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('label', [
            new Assert\NotBlank([
                'message' => "Le nom du planning est obligatoire"
            ]),
            new Assert\Length([
                'min' => 2,
                'max' => 32,
                'minMessage' => "Le nom du planning est trop court",
                'maxMessage' => "Le nom du planning est trop long",
            ])
        ]);
    }

    /**
     * @return array
     */
    public function getTips(): array
    {
        if ($this->getIlots()->isEmpty()) {
            $this->addTip('warning', "Aucun îlot n'est attaché");
        } else {
            $count = $this->getIlots()->count();
            $plural = ($count > 1) ? 's' : "";

            $this->addTip('info', "Ce planning regroupe {$count} îlot{$plural}");
        }

        if ($this->getSeries()->isEmpty()) {
            $this->addTip('warning', "Aucun Gestionnaire n'est attaché");
        }

        return parent::getTips();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlanningEntity
     */
    public function setId(int $id): PlanningEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $string
     * @return PlanningEntity
     */
    public function setSlug(string $string): PlanningEntity
    {
        $this->slug = (new Slugify())
            ->slugify($string, '-');

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
     * @param string $label
     * @return PlanningEntity
     */
    public function setLabel(string $label): PlanningEntity
    {
        $label = strip_tags($label);

        $this->label = $label;
        $this->setSlug($label);

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return PlanningEntity
     */
    public function setColor(string $color): PlanningEntity
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return ArrayCollection|SerieEntity[]
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param SerieEntity $serie
     * @return PlanningEntity
     */
    public function addSerie(SerieEntity $serie): PlanningEntity
    {
        $this->series->add($serie);
        return $this;
    }

    /**
     * @param SerieEntity[] $series
     * @return ArrayCollection|SerieEntity[]
     */
    public function attachSeries(array $series)
    {
        if (!empty($series)) {
            $existingSeries = explode('$azerty$', implode('$azerty$', $this->series->toArray()));
            $addingSeries = explode('$azerty$', implode('$azerty$', $series));

            // on supprime les séries déjà attachées
            foreach ($existingSeries as $i => $serie_id) {
                if (in_array($serie_id, $addingSeries) === false) {
                    $this->series->remove($i);
                }
            }

            // on ajoute les nouvelles séries qu'on n'a pas déjà
            foreach ($addingSeries as $j => $serie_id) {
                if (in_array($serie_id, $existingSeries) === false) {
                    $this->addSerie($series[$j]);
                }
            }
        } else {
            $this->series->clear();
        }

        return $this->series;
    }

    /**
     * @return ArrayCollection|IlotEntity[]
     */
    public function getIlots()
    {
        return $this->ilots;
    }

    /**
     * @param IlotEntity $ilot
     * @return PlanningEntity
     */
    public function addIlot(IlotEntity $ilot): PlanningEntity
    {
        $this->ilots[] = $ilot;
        return $this;
    }

    /**
     * @return PlanningChargeEntity[]
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * @param PlanningChargeEntity $charge
     * @return PlanningEntity
     */
    public function addCharge(PlanningChargeEntity $charge): PlanningEntity
    {
        $this->charges[] = $charge;
        return $this;
    }
}