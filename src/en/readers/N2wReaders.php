<?php
/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/19/17
 * Time: 11:14 PM
 */

namespace chitwarnold\n2w\en\readers;

use chitwarnold\n2w\en\readers\N2wReadersException;
use chitwarnold\n2w\en\readers\N2wReadersInterface;

/**
 * Class N2wReaders - allows for the reading of the packet schemata and translating to words accordingly
 * @todo define an Interface that this
 * @package chitwarnold\n2w\en\readers
 */
class N2wReaders implements N2wReadersInterface
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
     * gets the value on the hundreds' place value
     * @param $challenge_packet - the  challenge packet has got to  have a length of 3
     * @return bool|string $a - the string found in the hundreds place value, calling it a string because we are handling as string
     */
    public function getA($challenge_packet)
    {// N2wReaders::getA();
        $a = null;
        if(strlen($challenge_packet) !== 3){
            exit('invalid A String');
        }else{
            $a = substr($challenge_packet,0,1);
        }
        return $a;
    } // N2wReaders::getA();


    /**
     * gets the value on the tens' place value
     * @param $challenge_packet - the  challenge packet has got to  have a length of 3
     * @return bool|string $b - the string found in the tens place value, calling it a string because we are handling as string
     */
    public function getB($challenge_packet)
    { // N2wReaders::getB();
        $b = null;
        if(strlen($challenge_packet) !== 3){
            exit('invalid B String');
        }else{
            $b = substr($challenge_packet,1,1);
        }
        return $b;
    } // N2wReaders::getB();



    /**
     * @param $challenge_packet - the challenge packet string
     * @return string $packet_spelling - str of words representing the packet value
     */
    public final function ZZZSchemaReader($challenge_packet)
    { // N2wReaders::ZZZSchemaReader();
        $packet_spelling = "";
        return $packet_spelling;
    }  // N2wReaders::ZZZSchemaReader();

}