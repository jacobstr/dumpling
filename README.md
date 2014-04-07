Dumpling
--------
A small utility for generating variable dumps. PHP's built-in print_r goes'
haywire when using complex object graphs. It also outputs directly to stdout by
default.

Originally based off the code here: http://ca2.php.net/print_r#78851. Wrapped
up and packaged for the composer era.

Usage
-----
Dumpling
```
>> echo Dumpling::d($cyclical_line_point_object);

Plop\Tests\Line Object (
    [points] => Array (
        [0] => Plop\Tests\Point Object (
            [x] => 5
            [y] => 10
            [line] => Nested Plop\Tests\Line Object
        )
    )
)

>> echo Dumpling::d(array_slice(debug_backtrace(),0,2),2);

Array (
    [0] => Array (
        [function] => should_dump_a_recursive_object_at_depth_2
        [class] => Dumpling\Tests\DumplingTest
        [object] => Nested Dumpling\Tests\DumplingTest Object
        [type] => ->
        [args] => Nested Array
    )
    [1] => Array (
        [file] => /Users/jacob/Work/dumpling/vendor/phpunit/phpunit/PHPUnit/Framework/TestCase.php
        [line] => 983
        [function] => invokeArgs
        [class] => ReflectionMethod
        [object] => Nested ReflectionMethod Object
        [type] => ->
        [args] => Nested Array
    )
)
```
