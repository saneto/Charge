<?php
namespace App\Entity\Planning;

use App\Entity\Doctrine\IlotProcessingEntity;
use App\Twig\TwigExtensions;

class IlotRemainingChargeEvent extends PlanningEventEntity
{
    /**
     * @var IlotProcessingEntity
     */
    private $quantity;

    protected $editable = false;
    protected $startEditable = false;
    protected $durationEditable = false;
    protected $textColor = "#FFFFFF";
    protected $tooltip = "Consulter les prélèvements";

    /**
     * @return string
     */
    public static function getEventType(): string
    {
        return "chargeDetails";
    }

    /**
     * IlotEvent constructor.
     *
     * @param IlotProcessingEntity $quantity
     * @param bool             $hydrate
     */
    public function __construct(IlotProcessingEntity $quantity, bool $hydrate = true)
    {
        parent::__construct();
        $this->quantity = $quantity;

        if($hydrate) {
            // on clone l'objet pour ne pas le modifier
            $start = clone $this->quantity->getDate();

            $this->setData('quantities', [
                    'original' => $this->quantity->getOriginalQuantity(),
                    'commandes' => $this->quantity->getCommandesQuantity(),
                    'remaining' => $this->quantity->getQuantity()
                ])
                ->setStart($this->quantity->getDate())
                ->setEnd($start->add(new \DateInterval('P01D')));

            if ($this->quantity->getOriginalQuantity() === $this->quantity->getQuantity()) {
                $this->setTitle("Pas de mouvements");
                $this->setColor('#304FFE');
            } else {
                $this->_setTitle();
                $this->setColor(TwigExtensions::colorizeQuantityFilter($this->quantity->getQuantity(), true));
            }
        }
    }

    /**
     * @return PlanningEventEntity
     */
    private function _setTitle(): PlanningEventEntity
    {
        $title = "Prlv: {$this->quantity->getCommandesQuantity()}";
        $title .= " | Rst. {$this->quantity->getQuantity()}/{$this->quantity->getOriginalQuantity()}";

        return parent::setTitle($title);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
