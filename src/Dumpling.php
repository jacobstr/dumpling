<?php namespace Dumpling;

use Closure;
use ReflectionFunction;

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
        $this->inspect($value);
        $result = rtrim(implode("", $this->result), "\n");
        return $result;
    }

    /**
     * Static factory method.
     *
     * @param mixed $options If a number is used, it is the maximum depth.
     */
    public static function D($value, $options=array())
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

    private function inspect($subject)
    {
        $this->level++;

        if ($subject instanceof Closure) {
            $this->inspectClosure($subject);
        } elseif (is_object($subject)) {
            $this->inspectObject($subject);
        } elseif (is_array($subject)) {
            $this->inspectArray($subject);
        } else {
            $this->inspectPrimitive($subject);
        }

        $this->level--;
    }

    private function inspectPrimitive($subject)
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

    private function inspectObject($subject)
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
                $this->inspect($val);
            }
        }

        $this->result[] = str_repeat(" ", ($this->level - 1) * 4) . ")\n";
    }

    private function inspectArray($subject)
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
                $this->inspect($val);
            }
        }

        $this->result[] = str_repeat(" ", ($this->level - 1) * 4) . ")\n";
    }

    /**
     * Inspired by: http://www.metashock.de/2013/05/dump-source-code-of-closure-in-php/
     */
    private function inspectClosure($subject)
    {
        $reflection = new ReflectionFunction($subject);
        $params = array_map(function($param) {
            return ($param->isPassedByReference() ? '&$' : '$').$param->name;
        }, $reflection->getParameters());
        $this->result[] = 'Closure ('.implode(", ", $params).') { ... }'."\n";
    }
}
