<?php namespace Dumpling;

class Dumpling
{
    // Stateful variables populated during a dump.
    protected $stack = array();
    protected $level = 0;
    protected $result = array();

    public function __construct($options = array())
    {
        $this->options = array_merge(array(
            'depth' => 3,
            'ignore' => array(),
        ), $options);

        $this->depth = $this->options['depth'];
    }

    /**
     * Generates a string represention of $value, up to a given depth.
     *
     * @param mixed $value The variable you wish to inspect.
     *
     * @return string
     *
     */
    public function dump($value)
    {
        $this->reset();
        $this->delve($value);
        $result = rtrim(implode("", $this->result), "\n");
        return $result;
    }

    /**
     * Static factory method.
     *
     * @param mixed $options If a number is used, it is the maximum depth.
     */
    public static function d($value, $options=array())
    {
        if (is_numeric($options)) {
            $options = array('depth' => $options);
        } elseif (empty($options)) {
            $options = array();
        }
        $plop = new Dumpling($options);
        return $plop->dump($value);
    }

    private function reset()
    {
        $this->level = 0;
        $this->stack = array();
        $this->result = array();
    }

    private function isIgnoredKey($key)
    {
        return in_array($key, $this->options['ignore']);
    }

    private function formatKey($key)
    {
        $result = array();

        $result[] = str_repeat(" ", $this->level * 4) . '[';
        if ($key{0} == "\0") {
            $keyParts = explode("\0", $key);
            $result[] = $keyParts[2] . (($keyParts[1] == '*') ? ':protected' : ':private');
        } else {
            $result[] = $key;
        }

        $result[] = "] => ";
        return implode("", $result);
    }

    private function delve($subject)
    {
        $this->level++;

        if (is_object($subject)) {
            $this->delveObject($subject);
        } elseif (is_array($subject)) {
            $this->delveArray($subject);
        } else {
            $this->delvePrimitive($subject);
        }

        $this->level--;
    }

    private function delvePrimitive($subject)
    {
        if ($subject === true) {
            $subject = '(bool)true';
        } elseif ($subject ===  false) {
            $subject = '(bool)false';
        } elseif ($subject === null) {
            $subject = '(null)';
        }

        $this->result[] = $subject . "\n";
    }

    private function delveObject($subject)
    {

        // Depth Guard
        if ($this->level > $this->depth) {
            $this->result[] = "Nested ".get_class($subject)." Object\n";
            return;
        }

        $this->result[] = get_class($subject) . " Object (\n";
        $subject = (array) $subject;

        foreach ($subject as $key => $val) {
            if ($this->isIgnoredKey($key) === false) {
                $this->result[] = $this->formatKey($key);
                $this->delve($val);
            }
        }

        $this->result[] = str_repeat(" ", ($this->level - 1) * 4) . ")\n";
    }

    private function delveArray($subject)
    {
        // Depth Guard
        if ($this->level > $this->depth) {
            $this->result[] = "Nested Array\n";
            return;
        }

        $this->result[] = "Array (\n";

        foreach ($subject as $key => $val) {
            if ($this->isIgnoredKey($key) === false) {
                $this->result[] = str_repeat(" ", $this->level * 4) . '[' . $key . '] => ';
                $this->delve($val);
            }
        }

        $this->result[] = str_repeat(" ", ($this->level - 1) * 4) . ")\n";
    }
}
