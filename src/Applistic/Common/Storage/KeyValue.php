<?php

namespace Applistic\Common\Storage;

use Closure;
use Countable;
use Iterator;

/**
 * A wrapper class for arrays.
 *
 * @author Frederic Filosa <filosa.frederic@gmail.com>
 * @copyright (c) 2013, applistic.com
 */
class KeyValue implements Countable, Iterator
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

    /**
     * The items keys.
     *
     * @var array
     */
    protected $keys = array();

    /**
     * The iterator index.
     *
     * @var integer
     */
    protected $index = 0;


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
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
        $this->keys = array_keys($this->items);
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
     * @return void
     */
    public function remove($key)
    {
        unset($this->items[$key]);
        $this->keys = array_keys($this->items);
    }

    /**
     * Increases a value.
     *
     * @param  string  $key    The key.
     * @param  numeric $amount The amount to add.
     * @return void
     */
    public function increase($key, $amount = 1)
    {
        $this->changeNumericValue($key, $amount);
    }

    /**
     * Decreases a value.
     *
     * @param  string  $key    The key.
     * @param  numeric $amount The amount to remove.
     * @return void
     */
    public function decrease($key, $amount = -1)
    {
        $this->changeNumericValue($key, $amount);
    }

    /**
     * Randomizes.
     *
     * @return void
     */
    public function shuffle()
    {
        shuffle($this->items);
        $this->keys = array_keys($this->items);
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
     * Removes all items and restore the index.
     *
     * @return void
     */
    public function clear()
    {
        $this->items = array();
        $this->rewind();
        $this->keys = array_keys($this->items);
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
        array_walk($this->items, $handler);
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


// ===== ITERATOR INTERFACE ====================================================

    /**
     * Returns the item at the current index.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->items[$this->keys[$this->index]];
    }

    /**
     * Returns the current index.
     *
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Increase the index.
     *
     * @return void
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Reset the index.
     *
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Checks the validity of the index.
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->index < count($this->items));
    }


// ===== PROTECTED METHODS =====================================================

    /**
     * Changes a numeric value by a certain amount.
     *
     * @param  string  $key    The key.
     * @param  integer $amount The amount to add or remove.
     * @return void
     */
    protected function changeNumericValue($key, $amount)
    {
        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException("\$amount must be numeric.");
        }

        $v = $this->items[$key];

        if (!is_numeric($v)) {
            throw new \Exception("\The \$key's value must be numeric.");
        }

        $this->items[$key] = $v + $amount;
    }

// ===== PRIVATE METHODS =======================================================
}