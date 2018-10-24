<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CommandeProcessingEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var CommandeEntity
     */
    protected $bill;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var DepotEntity
     */
    protected $depot;

    /**
     * @var IlotEntity
     */
    protected $ilot;

    /**
     * PROPRIÉTÉ PUBLIC POUR PASSER OUTRE LES ERREURS DE RECHERCHES VIA LE FILTRAGE.
     * Pas le temps de faire du refactoring pour le renommer en processingAt.
     *
     * @var \DateTime
     */
    public $processing_at;

    /**
     * @var array
     */
    protected $excluded = ['bill'];

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getIlot();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints(
            'quantity', [
                new Assert\GreaterThanOrEqual(
                    [
                        'value' => 1,
                        'message' => "La quantité minimale doit être de 1 pièce"
                    ]
                )
            ]
        );
    }

    /**
     * On ajoute nous même le champ "processing_at" vu qu'il est en public il est ignoré par défaut.
     * @return array
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['processing_at'] = $this->getProcessingAt()->format(DATE_ISO8601);

        return $data;
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
     * @return CommandeProcessingEntity
     */
    public function setId(int $id): CommandeProcessingEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return CommandeEntity
     */
    public function getBill(): CommandeEntity
    {
        return $this->bill;
    }

    /**
     * @param CommandeEntity $bill
     * @return CommandeProcessingEntity
     */
    public function setBill(CommandeEntity $bill): CommandeProcessingEntity
    {
        $this->bill = $bill;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return CommandeProcessingEntity
     */
    public function setQuantity(int $quantity): CommandeProcessingEntity
    {
        $this->quantity = $quantity;
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
     * @return CommandeProcessingEntity
     */
    public function setDepot(DepotEntity $depot): CommandeProcessingEntity
    {
        $this->depot = $depot;
        return $this;
    }

    /**
     * @return IlotEntity
     */
    public function getIlot(): IlotEntity
    {
        return $this->ilot;
    }

    /**
     * @param IlotEntity $ilot
     * @return CommandeProcessingEntity
     */
    public function setIlot(IlotEntity $ilot): CommandeProcessingEntity
    {
        $this->ilot = $ilot;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getProcessingAt(): \DateTime
    {
        return $this->processing_at;
    }

    /**
     * @param \DateTime $processing_at
     * @return CommandeProcessingEntity
     */
    public function setProcessingAt(\DateTime $processing_at): CommandeProcessingEntity
    {
        $this->processing_at = $processing_at;
        return $this;
    }
}