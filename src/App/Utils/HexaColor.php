<?php
namespace App\Utils;

use Core\Entity\Entity;

class HexaColor extends Entity
{
    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    private $rgb = null;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getColor();
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
     * @return HexaColor
     */
    public function setColor(?string $color): self
    {
        if (substr($color, 0, 1) !== '#') {
            $color = "#{$color}";
        }

        $this->color = $color;
        return $this;
    }

    /**
     * @return string
     */
    public function toRgb() 
    {
        if($this->rgb === null) {
            list($r, $g, $b) = sscanf($this->color, "#%02x%02x%02x");
            // return ['r' => $r, 'g' => $g, 'b' => $b];
            $this->rgb = "{$r},{$g},{$b}";
        }

        return $this->rgb;
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }
}
