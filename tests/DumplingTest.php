<?php
namespace Dumpling\Tests;

use Dumpling\Dumpling;

class Point
{
    public $x;
    public $y;
    public $line;
}

class Line
{
    public $points;
}

class DumplingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_dump_an_array()
    {
        $actual = Dumpling::D(array("hello"=>"world"));
        $expected = <<<EOD
Array (
    [hello] => world
)
EOD;
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_dump_an_object()
    {
        $point = new Point();
        $point->x = 5;
        $point->y = 10;

        $actual = Dumpling::D($point);
        $expected = <<<EOD
Dumpling\Tests\Point Object (
    [x] => 5
    [y] => 10
    [line] => (null)
)
EOD;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_dump_a_recursive_object_at_depth_3()
    {
        $point = new Point();
        $point->x = 5;
        $point->y = 10;

        $line = new Line();
        $line->points[] = $point;

        $point->line = $line;

        $actual = Dumpling::D($line);
        $expected = <<<EOD
Dumpling\Tests\Line Object (
    [points] => Array (
        [0] => Dumpling\Tests\Point Object (
            [x] => 5
            [y] => 10
            [line] => Nested Dumpling\Tests\Line Object
        )
    )
)
EOD;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_dump_a_recursive_object_at_depth_2()
    {
        $point = new Point();
        $point->x = 5;
        $point->y = 10;

        $line = new Line();
        $line->points[] = $point;

        $point->line = $line;

        $actual = Dumpling::D($line, 2);
        $expected = <<<EOD
Dumpling\Tests\Line Object (
    [points] => Array (
        [0] => Nested Dumpling\Tests\Point Object
    )
)
EOD;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_dump_a_closure()
    {
        if(true && $fn = function(
            $a,
            $b = 0
        ) {
            // MATH!
            return $a + 1;
        }) {

        };

        $actual = Dumpling::D($fn, 2);
        $expected = <<<EOD
EOD;

        $this->assertRegExp('/.*function.*return \$a \+ 1.*/s', $actual);

    }
}
