<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CommandeEntity extends Entity
{
    /**
     * Mapping pour les filtres de recherches.
     */
    const QUERYBUILDER_MAPPING = [
        'commande_id' => 'id',
        'commande_vendor' => 'vendor',
        'commande_serie_id' => 'serie',
        'commande_bl_id' => 'blId',
        'commande_client_name' => 'clientName',
        'commande_client_reference' => 'clientReference',
        'commande_quantity' => 'quantity',
        'commande_cas_type' => 'casType',
        'commande_machine_ts' => 'machineTs',
        'commande_depart_ts' => 'departTs',
        'commande_sous_traitant' => 'sousTraitant',
        'commande_transport' => 'transport',
        'processings.processing_at' => 'processings.processing_at',
        'commande_delivery_at' => 'deliveryAt',
        'commande_created_at' => 'createdAt',
        'commande_reception_at' => 'receptionAt'
    ];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var UserEntity
     */
    protected $vendor;

    /**
     * @var SerieEntity
     */
    protected $serie;

    /**
     * @var int
     */
    protected $blId;

    /**
     * @var mixed
     */
    protected $clientName;

    /**
     * @var mixed
     */
    protected $clientReference;

    /**
     * @var int
     */
    protected $quantity = 0;

    /**
     * @var null|CommentEntity
     */
    protected $sousTraitant;

    /**
     * @var null|string
     */
    protected $transport;

    /**
     * @var null|string
     */
    protected $machineTs;

    /**
     * @var null|\DateTime
     */
    protected $departTs;

    /**
     * @var \DateTime
     */
    protected $processingAt;

    /**
     * @var \DateTime|null
     */
    protected $deliveryAt;

    /**
     * @var mixed
     */
    protected $casType;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var null|\DateTime
     */
    protected $receptionAt;

    /**
     * @var null|\DateTime
     */
    protected $canceledAt;

    /**
     * @var CommentEntity[]
     */
    protected $comments;

    /**
     * @var CommandeProcessingEntity[]
     */
    protected $processings;

    /**
     * @var array
     */
    protected $excluded = ['comments', 'processings', 'serie'];

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * CommandeEntity constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->processings = new ArrayCollection();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('id', [
            new Assert\GreaterThan(['value' => 0])
        ]);
        $metadata->addPropertyConstraints('blId', [
            new Assert\GreaterThan(['value' => 0])
        ]);
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
     * @return CommandeEntity
     */
    public function setId(int $id): CommandeEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return UserEntity
     */
    public function getVendor(): UserEntity
    {
        return $this->vendor;
    }

    /**
     * @param UserEntity $vendor
     * @return CommandeEntity
     */
    public function setVendor(UserEntity $vendor): CommandeEntity
    {
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * @return SerieEntity
     */
    public function getSerie(): SerieEntity
    {
        return $this->serie;
    }

    /**
     * @param SerieEntity $serie
     * @return CommandeEntity
     */
    public function setSerie(SerieEntity $serie): CommandeEntity
    {
        $this->serie = $serie;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlId(): int
    {
        return $this->blId;
    }

    /**
     * @param int $blId
     * @return CommandeEntity
     */
    public function setBlId(int $blId): CommandeEntity
    {
        $this->blId = $blId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * @param mixed $clientName
     * @return CommandeEntity
     */
    public function setClientName($clientName)
    {
        if (!empty($clientName)) {
            $this->clientName = strtoupper($clientName);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientReference()
    {
        return $this->clientReference;
    }

    /**
     * @param mixed $clientReference
     * @return CommandeEntity
     */
    public function setClientReference($clientReference)
    {
        if (!empty($clientReference)) {
            $this->clientReference = strtoupper($clientReference);
        }

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
     * @deprecated
     * @param int $quantity
     *
     * @return CommandeEntity
     */
    public function setQuantity(int $quantity): CommandeEntity
    {
        throw new \InvalidArgumentException("Cannot set quantity using setQuantity(). Please insert entry in table `commandes_processing` instead.");
    }

    /**
     * @return null|string
     */
    public function getSousTraitant(): ?string
    {
        return $this->sousTraitant;
    }

    /**
     * @param null|string $sousTraitant
     * @return CommandeEntity
     */
    public function setSousTraitant(?string $sousTraitant)
    {
        $sousTraitant = strip_tags($sousTraitant);

        if (!empty($sousTraitant)) {
            $this->sousTraitant = strtoupper($sousTraitant);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTransport(): ?string
    {
        return $this->transport;
    }

    /**
     * @param null|string $transport
     * @return CommandeEntity
     */
    public function setTransport(?string $transport): CommandeEntity
    {
        $transport = strtoupper(strip_tags($transport));

        $this->transport = (!empty($transport)) ? $transport : null;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getMachineTs(): ?string
    {
        return (string) $this->machineTs;
    }

    /**
     * @param null|string $machineTs
     * @return CommandeEntity
     */
    public function setMachineTs(string $machineTs)
    {
        $machineTs = strip_tags($machineTs);

        if (!empty($machineTs)) {
            $this->machineTs = strtoupper($machineTs);
        }

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDepartTs(): ?\DateTime
    {
        return $this->departTs;
    }

    /**
     * @param \DateTime|null $departTs
     * @return CommandeEntity
     */
    public function setDepartTs(?\DateTime $departTs)
    {
        $this->departTs = $departTs;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getProcessingAt(): ?\DateTime
    {
        return $this->processingAt;
    }

    /**
     * @param \DateTime $processingAt
     * @return CommandeEntity
     */
    public function setProcessingAt(\DateTime $processingAt): CommandeEntity
    {
        $this->processingAt = $processingAt;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveryAt(): ?\DateTime
    {
        return $this->deliveryAt;
    }

    /**
     * @param \DateTime|null $deliveryAt
     * @return CommandeEntity
     */
    public function setDeliveryAt(?\DateTime $deliveryAt): CommandeEntity
    {
        $this->deliveryAt = $deliveryAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCasType()
    {
        return $this->casType;
    }

    /**
     * @param mixed $casType
     * @return CommandeEntity
     */
    public function setCasType($casType)
    {
        if (!empty($casType)) {
            $this->casType = $casType;
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return CommandeEntity
     */
    public function setCreatedAt(\DateTime $createdAt): CommandeEntity
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getReceptionAt(): ?\DateTime
    {
        return $this->receptionAt;
    }

    /**
     * @param \DateTime $receptionAt
     * @return CommandeEntity
     */
    public function setReceptionAt(\DateTime $receptionAt)
    {
        $this->receptionAt = $receptionAt;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCanceledAt(): ?\DateTime
    {
        return $this->canceledAt;
    }

    /**
     * @param \DateTime $canceledAt
     * @return CommandeEntity
     */
    public function setCanceledAt(\DateTime $canceledAt)
    {
        $this->canceledAt = $canceledAt;
        return $this;
    }

    /**
     * @return ArrayCollection|CommentEntity[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param CommentEntity $comment
     * @return CommentEntity
     */
    public function addComment(CommentEntity $comment): CommentEntity
    {
        $this->comments[] = $comment;
        return $comment;
    }

    /**
     * @return CommandeProcessingEntity[]
     */
    public function getProcessings()
    {
        return $this->processings;
    }

    /**
     * @param CommandeProcessingEntity $processing
     */
    public function addProcessing(CommandeProcessingEntity $processing): void
    {
        $this->processings[] = $processing;
    }

    /**
     * @return bool
     */
    public function isCanceled(): bool
    {
        return ($this->getCanceledAt() instanceof \DateTime);
    }

    /**
     * @return bool
     */
    public function isReceptionned(): bool
    {
        return ($this->getReceptionAt() instanceof \DateTime);
    }
}