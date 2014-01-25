<?php

namespace Applistic\Common;

use Closure;
use Countable;
use ArrayAccess;

/**
 * A basic key/value store.
 *
 * @author Frederic Filosa <fred@applistic.com>
 * @copyright (c) 2014, Frederic Filosa
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class KeyValue implements Countable, ArrayAccess
{
// ===== CONSTANTS =============================================================
// ===== STATIC PROPERTIES =====================================================
// ===== STATIC FUNCTIONS ======================================================
// ===== PROPERTIES ============================================================

    /**
     * The items.
     *
     * @var array
     */
    protected $items = array();


// ===== CONSTRUCTOR ===========================================================

    /**
     * Constructs a KeyValue object.
     *
     * @param array $items Optional initial items.
     */
    public function __construct(array $items = null)
    {
        if (!is_null($items)) {
            $this->items = $items;
        }
    }


// ===== PUBLIC METHODS ========================================================

    /**
     * Checks if the $key is registered.
     *
     * The value associated with the $key can be null.
     *
     * @param  string  $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Sets the value of a key.
     *
     * @param string|int $key
     * @param mixed $value
     * @return \Applistic\Common\KeyValue
     */
    public function set($key, $value)
    {
        if (!is_string($key)) {
            $message = "\$key must be a string.";
            throw new \InvalidArgumentException($message);
        }

        $this->items[$key] = $value;

        return $this;
    }

    /**
     * Returns the value of a key.
     *
     * @param  string|int $key
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        } else {
            return $default;
        }
    }

    /**
     * Removes a key.
     *
     * @param  string $key The key to remove.
     * @return \Applistic\Common\KeyValue
     */
    public function remove($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Increases a value.
     *
     * @param  string  $key    The key.
     * @param  numeric $amount The amount to add.
     * @return \Applistic\Common\KeyValue
     */
    public function increase($key, $amount = 1)
    {
        $this->changeNumericValue($key, $amount);

        return $this;
    }

    /**
     * Decreases a value.
     *
     * @param  string  $key    The key.
     * @param  numeric $amount The amount to remove.
     * @return \Applistic\Common\KeyValue
     */
    public function decrease($key, $amount = 1)
    {
        $this->changeNumericValue($key, $amount, true);

        return $this;
    }

    /**
     * Randomizes.
     *
     * @return \Applistic\Common\KeyValue
     */
    public function shuffle()
    {
        shuffle($this->items);

        return $this;
    }

    /**
     * Sorts the keys alphabetically.
     *
     * @return \Applistic\Common\KeyValue
     */
    public function sort($order = 'asc')
    {
        if ($order == 'desc') {
            krsort($this->items);
        } else {
            ksort($this->items);
        }

        return $this;
    }

    /**
     * Returns the items as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Returns a JSON representation.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->items);
    }

    /**
     * Removes all items.
     *
     * @return \Applistic\Common\KeyValue
     */
    public function clear()
    {
        $this->items = array();
        $this->rewind();

        return $this;
    }

    /**
     * Walks through the items to perform actions.
     *
     * @see array_walk
     *
     * @param  Closure $handler The function.
     * @return void
     */
    public function each(Closure $handler)
    {
        foreach ($this->items as $key => $value) {
            if ($handler($key, $value) === false) {
                break;
            }
        }
    }

// ===== ARRAY-ACCESS INTERFACE ================================================

    /**
     * [booleanoffsetExists description]
     * @param  mixed  $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * [mixedoffsetGet description]
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * [voidoffsetSet description]
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset , $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * [voidoffsetUnset description]
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

// ===== COUNTABLE INTERFACE ===================================================

    /**
     * Returns the number of keys.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

// ===== PROTECTED METHODS =====================================================

    /**
     * Changes a numeric value by a certain amount.
     *
     * @param  string  $key    The key.
     * @param  integer $amount The amount to add or remove.
     * @return void
     */
    protected function changeNumericValue($key, $amount, $negative = false)
    {
        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException("\$amount must be numeric.");
        }

        $v = $this->items[$key];

        if (!is_numeric($v)) {
            throw new \Exception("\The \$key's value must be numeric.");
        }

        if ($negative === true) {
            $this->items[$key] = $v - $amount;
        } else {
            $this->items[$key] = $v + $amount;
        }

    }

// ===== PRIVATE METHODS =======================================================
}