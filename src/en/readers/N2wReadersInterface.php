<?php
/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/19/17
 * Time: 11:29 PM
 */

namespace chitwarnold\n2w\en\readers;

/**
 * Class N2wReadersInterface - an interface to be implemented by all readers
 * @package chitwarnold\n2w\en\readers
 */
interface N2wReadersInterface
{
    /**
     * packet schema placeholder for 0(zero)
     */
    const PACKET_SCHEMA_PLACEHOLDER_ZERO = "Z";
    /**
     * packet schema placeholder for non-zero numbers
     */
    const PACKET_SCHEMA_PLACEHOLDER_NON_ZERO_NUMBER = "N";







    /**
     * reads the first left most value of the packet ( i.e abc in 789, a = 7),
     * the hundreds place value of tri-character packet
     * @param $challenge_packet
     * @return mixed
     */
    public function getA($challenge_packet);


    /**
     * reads the first middle value of the packet ( i.e abc in 789, b = 8),
     * the tens place value of a tri-character packet
     * @param $challenge_packet
     * @return mixed
     */
    public function getB($challenge_packet);


    /**
     * reads the first right most value of the packet ( i.e abc in 789, c = 9),
     * the ones place value of a tri-character packet
     * @param $challenge_packet
     * @return mixed
     */
    public function getC($challenge_packet);





    /**
     * ZZZSchemaReader - reads schemata that is like 000 and resolves the words and returns that words
     * called when a $challenge_packet looks like 000
     * @param $challenge_packet - the challenge packet string
     * @return string $packet_spelling - str of words representing the packet value
     */
    public function ZZZSchemaReader($challenge_packet);

    /**
     * ZZNSchemaReader -  called when a $challenge_packet looks like 001 to 009 (a=>0,b=>0,c=>9)
     *  this is used to resolve the ones
     * @param $challenge_packet - the challenge packet string
     * @return mixed
     */
    public function ZZNSchemaReader($challenge_packet)




}