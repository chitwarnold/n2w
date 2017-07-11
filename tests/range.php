<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/20/17
 * Time: 12:10 AM
 *
 *
 *
 *
 * will help out read the number that President JacobZuma was struggling to pronounce.
 * @link https://www.youtube.com/watch?v=nqNa6992ih4
 * @link https://www.youtube.com/watch?v=nHh9VjyyEYA
 *
 */
use chitwarnold\n2w\en\N2w;
use chitwarnold\n2w\en\readers\N2wReaders;

// get some spellcheck

/**
 *  Running the test from the php command line
 *  0. download this library
 *  1. Run $ composer install # to install the autoload
 *  2. test the library
 *     $ php zumatest.php
 * # feel free to to update the
 *
 * @todo implement threads on this to ensure it does take up too much memory for long tasks
 *
 */

$_spelling_bee = new N2w();
$decimal_point  = 2;
$start = 100;
$stop = 200;
echo "++++++++++++++++++++++++++++++++++++++++++++++++++++++".PHP_EOL;
echo "| Range Counting challenge From $start to $stop |".PHP_EOL;
echo "++++++++++++++++++++++++++++++++++++++++++++++++++++++".PHP_EOL;


for($i = $start; $i <= $stop; $i++)
{
    $spell = $_spelling_bee->updateChallenge($i,$decimal_point)->spell();
    echo "$i."." ".$spell.PHP_EOL;
}