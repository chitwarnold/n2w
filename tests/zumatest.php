<?php
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

$_spelling_bee = new N2w();
$solution = $_spelling_bee->solve(200000,2);
echo $solution;