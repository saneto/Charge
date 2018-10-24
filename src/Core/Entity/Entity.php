<?php
namespace Core\Entity;

use App\Entity\Planning;
use Doctrine\ORM\PersistentCollection;

abstract class Entity implements EntityInterface
{
    /**
     * @var null|array
     */
    private $_properties = null;

    /**
     * @var array
     */
    private $_tips = [];

    /**
     * @var bool
     */
    protected static $to_event = false;

    /**
     * @var array
     */
    protected $excluded = [];

    /**
     * @param $property
     * @return null|string
     */
    private function _getGetter($property): ?string
    {
        $property = str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));

        if (method_exists($this, 'is' . $property)) {
            return 'is' . $property;
        } elseif (method_exists($this, 'get' . $property)) {
            return 'get' . $property;
        }

        return null;
    }

    /**
     * Retourne des informations liées à l'entité.
     * Exemple: des données manquantes ou des recommandations.
     *
     * @return array
     */
    public function getTips(): array
    {
        return $this->_tips;
    }

    /**
     * @return bool
     */
    function hasTips(): bool
    {
        return empty($this->_tips);
    }


    /**
     * Ajoute un tip.
     *
     * @param string $type
     * @param string $message
     */
    public function addTip(string $type, string $message): void
    {
        $this->_tips[$type][] = $message;
    }


    /**
     * Get array copy of object.
     *
     * @param array $excludes
     * @return array
     */
    public function getArrayCopy(array $excludes = [])
    {
        if($this->_properties === null) {
            $copy = [];

            $reflection = new \ReflectionClass($this);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);

            foreach($properties as $k => $property) {
                if (in_array($property->getName(), $this->excluded) || $property->isStatic()) {
                    continue;
                }

                $getter = ucfirst($property->getName());

                if($reflection->hasMethod('is' . $getter)) {
                    $getter = 'is' . $getter;
                    $value = $this->{$getter}();
                } elseif($reflection->hasMethod('get' . $getter)) {
                    $getter = 'get' . $getter;
                    $value = $this->{$getter}();
                } else {
                    $value = $this->{$property->getName()};
                }

                if($value instanceof \DateTime) {
                    $value = $value->format(DATE_ISO8601);
                } elseif ($value instanceof PersistentCollection) {
                    $collection = [];
                    foreach ($value as $item) {
                        $collection[] = $item;
                    }
                    $value = $collection;
                }

                $copy[$property->getName()] = $value;
            }

            $this->_properties = $copy;
        }

        return $this->_properties;
    }

    public static function toEvent(): void
    {
        self::$to_event = true;
    }

    public static function toEntity(): void
    {
        self::$to_event = false;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $this->excluded[] = 'excluded';
        return $this->getArrayCopy($this->excluded);
    }

    public function offsetExists($offset)
    {
        return (property_exists($this, $offset));
    }

    public function offsetGet($offset)
    {
        if (($getter = $this->_getGetter($offset)) !== null) {
            return $this->{$getter}();
        } elseif (property_exists($this, $offset)) {
            return $this->{$offset};
        }

        return null;
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception("Cannot set Entity propertity (use setters instead)");
    }

    public function offsetUnset($offset)
    {
        throw new \Exception("Cannot unset Entity propertity");
    }
}
