<?php
namespace Core;

use Slim\Interfaces\CollectionInterface;
use Traversable;

class Collection implements CollectionInterface
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * ArrayContainer constructor.
     *
     * @param array $values
     */
    public function __construct(?array $values = [])
    {
        $this->replace($values);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @param string $key
     * @param null   $default
     * @return array|Collection|mixed|null
     */
    public function get($key, $default = null)
    {
        // on vérifie que la clef existe
        if($this->has($key)) {
            $values = $this->values;

            if(substr($key, 0, 1) === "'" && substr($key, -1) === "'") {
                $key = trim($key, "'");
                $values = $values[$key];
            } else {
                $ids = explode('.', $key);
                $sizeof_ids = sizeof($ids);

                for($i = 0; $i < $sizeof_ids; $i++) {
                    $current_id = $ids[$i];
                    $values = $values[$current_id];
                }
            }

            return (is_array($values)) ? new self($values) : $values;
        }

        return null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key): bool
    {
        if(substr($key, 0, 1) === "'" && substr($key, -1) === "'") {
            $key = trim($key, "'");
            return (array_key_exists($key, $this->values));
        }

        // on récupère la première des clefs demandées
        [$key, $ids] = explode('.', "{$key}.", 2);

        $ids = trim($ids, '.');
        $values = $this->values;

        // on vérifie que la clef $id existe dans les données courantes
        if(array_key_exists($key, $values)) {
            $values = $values[$key];

            // la clef $id existe et on n'en a pas d'autres à vérifier
            if(empty($ids)) {
                return true;
            } else {
                $ids = explode('.', $ids);
                $sizeof_ids = sizeof($ids);

                for($i = 0; $i < $sizeof_ids; $i++) {
                    $current_id = $ids[$i];
                    if(is_array($values) && array_key_exists($current_id, $values)) {
                        if($i === $sizeof_ids - 1) {
                            return true;
                        }
                        $values = $values[$current_id];
                        continue;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param array $items
     */
    public function replace(?array $items)
    {
        if(empty($items) || is_null($items)) {
            $this->values = [];

            return;
        }

        foreach($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->values;
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        unset($this->values[$key]);
    }

    /**
     *
     */
    public function clear()
    {
        $this->values = [];
    }

    /**
     * Retrieve an external iterator
     *
     * @link   http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since  5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * Count elements of an object
     *
     * @link   http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since  5.1.0
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * Whether a offset exists
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param  mixed $offset <p>
     *                       An offset to check for.
     *                       </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since  5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetget.php
     * @param  mixed $offset <p>
     *                       The offset to retrieve.
     *                       </p>
     * @return mixed Can return all value types.
     * @since  5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The
     *                      value
     *                      to
     *                      set.
     *                      </p>
     *
     * @return void
     * @since  5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param  mixed $offset <p>
     *                       The offset to unset.
     *                       </p>
     * @return void
     * @since  5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
