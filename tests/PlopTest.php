<?php
namespace Plop\Tests;

use Plop\Plop as P;

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

class PlopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_dump_an_array()
    {
        $actual = P::d(array("hello"=>"world"));
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

        $actual = P::d($point);
        $expected = <<<EOD
Plop\Tests\Point Object (
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

        $actual = P::d($line);
        $expected = <<<EOD
Plop\Tests\Line Object (
    [points] => Array (
        [0] => Plop\Tests\Point Object (
            [x] => 5
            [y] => 10
            [line] => Nested Plop\Tests\Line Object
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

        $actual = P::d($line, 2);
        $expected = <<<EOD
Plop\Tests\Line Object (
    [points] => Array (
        [0] => Nested Plop\Tests\Point Object
    )
)
EOD;

        $this->assertEquals($expected, $actual);
    }
}
