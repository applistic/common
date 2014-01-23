<?php

class String implements ArrayAccess
{
// ===== CONSTANTS =============================================================
// ===== STATIC PROPERTIES =====================================================
// ===== STATIC FUNCTIONS ======================================================
// ===== PROPERTIES ============================================================

    /**
     * The assembled string.
     *
     * @var string
     */
    protected $string = "";

    /**
     * The string's encoding.
     *
     * @var string
     */
    protected $encoding = "UTF-8";

// ===== ACCESSORS =============================================================
// ===== CONSTRUCTOR ===========================================================

    public function __construct($string = "", $encoding = "UTF-8")
    {
        if (!is_string($string) && !is_numeric($string)) {
            $message = "\$string";
            throw new \InvalidArgumentException($message);
        }

        $this->string = $string;

        if (is_string($encoding)) {
            $this->encoding = $encoding;
        } elseif (function_exists('mb_detect_encoding')) {
            $this->encoding = mb_detect_encoding($string);
        }
    }

// ===== PUBLIC METHODS ========================================================

    /**
     * Returns the string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }

    /**
     * Returns an array of the characters.
     *
     * @param  string $separator The string used as separator.
     * @return array
     */
    public function split($separator = "")
    {
        if (empty($separator)) {
            if (function_exists('mb_strlen')) {
                $len = mb_strlen($this->string, $this->encoding);
                $arr = array();
                $str = $this->string;
                while ($len > 0) {
                    $arr[] = mb_substr($str, 0, 1, $this->encoding);
                    $str   = mb_substr($str, 1, null, $this->encoding);
                    $len--;
                }
                return $arr;
            } else {
                return str_split($this->string, 1);
            }
        } else {
            return explode($separator, $this->string);
        }
    }

    /**
     * Returns the length of the string.
     *
     * @return int
     */
    public function length()
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($this->string, $this->encoding);
        } else {
            return strlen($this->string);
        }
    }

    /**
     * Returns the number of bytes used.
     *
     * @return int
     */
    public function bytesCount()
    {
        return strlen($this->string);
    }

    /**
     * Returns a substring starting at $start and ending at ($start + $length).
     *
     * @param  int $start
     * @param  int $length
     * @return string
     */
    public function substring($start, $length = null)
    {
        $this->checkArrayOffset($start);
        return $this->substr($start, $length);
    }

    /**
     * Returns a substring starting at $start and ending at $end.
     *
     * @param  int $start
     * @param  int $end
     * @return string
     */
    public function range($start, $end)
    {
        $this->checkArrayOffset($start, $end);
        $length = $end - $start + 1;
        return $this->substr($start, $length);
    }

    /**
     * Returns the offset to the first occurence of $search.
     *
     * Returns `false` in case of failure.
     *
     * @param  string $search
     * @return boolean|int
     */
    public function pos($search)
    {
        return $this->strpos($search);
    }

    /**
     * Appends a string.
     *
     * @param  mixed $value
     * @return void
     */
    public function append($value)
    {
        $this->string .= $value;
    }

    /**
     * Prepends a string.
     *
     * @param  mixed $value
     * @return void
     */
    public function prepend($value)
    {
        $this->string = $value.$this->string;
    }

// ===== ArrayAccess Implementation ============================================

    /**
     * Returns true if the provided offset is in the range of the string's length.
     *
     * @param  mixed  $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $this->checkArrayOffset($offset);
        return ($offset < $this->length());
    }

    /**
     * [mixedoffsetGet description]
     * @param  mixed  $offset [description]
     * @return mixed         [description]
     */
    public function offsetGet($offset)
    {
        $this->checkArrayOffset($offset);
        if ($offset >= $this->length()) {
            return null;
        }

        return $this->substr($offset, 1);
    }

    /**
     * [voidoffsetSet description]
     * @param  mixed  $offset [description]
     * @param  mixed  $value  [description]
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->checkArrayOffset($offset);

        $value = mb_convert_encoding($value, $this->encoding);

        if (($value == "") || is_null($value)) {
            $this->offsetUnset($offset);
        } if ($offset == 0) {
            $this->string = $value.$this->string;
        } elseif ($offset >= $this->length()) {
            $this->string .= $value;
        } else {
            $before = $this->substring(0, ($offset + 1));
            $after  = $this->substring($offset+1);
            $this->string = $before.$value.$after;
        }
    }

    /**
     * [voidoffsetUnset description]
     * @param  mixed  $offset [description]
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->checkArrayOffset($offset);
        array_splice($this->characters, $offset, 1);

        $this->update();
    }

// ===== PROTECTED METHODS =====================================================

    /**
     * Checks that the provided offset is an integer (positive or zero).
     * @param  int $offset
     * @return void
     * @throws \InvalidArgumentException If the validation fails.
     */
    final protected function checkArrayOffset()
    {
        $args = func_get_args();
        foreach ($args as $value) {
            if (!is_int($value) || ($value < 0)) {
                $message = "string offset must be a zero or positive integer.";
                throw new \InvalidArgumentException($message);
            }
        }
    }

    final protected function substr($offset, $length = null)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($this->string, $offset, $length, $this->encoding);
        } else {
            if (is_null($length)) {
                $length = PHP_INT_MAX;
            }
            return substr($this->string, $offset, $length);
        }
    }

    final protected function strpos($search)
    {
        if (function_exists('mb_strpos')) {
            return mb_strpos($this->string, $search);
        } else {
            return strpos($this->string, $search);
        }
    }

    final protected function splitted($value)
    {
        $v = "".$value;

        if (function_exists('mb_strlen')) {

            $enc   = "UTF-8";
            $len   = mb_strlen($v);
            $chars = array();

            while ($len) {
                $c = trim(mb_substr($v, 0, 1, $enc));
                if ($c != "") {
                    $chars[] = $c;
                }
                $v = mb_substr($v, 1, $len, $enc);
                $len--;
            }

            return $chars;

        } else {

            return str_split($v, 1);

        }
    }

// ===== PRIVATE METHODS =======================================================
}