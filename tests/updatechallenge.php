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
 *
 *
 */

$_spelling_bee = new N2w();
$number_to_spell = 2000000000200000 ;// two quadrillion,
$decimal_point  = 2;
$solution = $_spelling_bee->solve($number_to_spell,$decimal_point);
echo $solution.PHP_EOL;
echo "++++++++++++++++++++++++++++++++++++".PHP_EOL;
echo "| updating challenge to 500,000.00 |".PHP_EOL;
echo "++++++++++++++++++++++++++++++++++++".PHP_EOL;
$challenge_update = 500000;
$_spelling_bee->updateChallenge($challenge_update,$decimal_point);
echo "Updated Value to : ".$_spelling_bee->spell().PHP_EOL;

