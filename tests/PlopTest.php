<?php
namespace Plop\Tests;

use Plop\Plop as Plop;

class PlopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_print_an_array()
    {
        $actual = Plop::p(array("hello"=>"world"));
        $expected = <<<EOD
Array (
    [hello] => world
)
EOD;
        $this->assertEquals($expected, $actual);
    }
}
