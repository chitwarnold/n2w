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
     * N2wReaders constructor.
     */
    public function __construct()
    { // N2wReaders::__construct();

    } // N2wReaders::__construct();


    /**
     * factory for  initializing the reader in client code
     * @return N2wReaders
     */
    public final static function initializeReader()
    { // N2wReaders::initializeReader();
        return new N2wReaders();
    } // N2wReaders::initializeReader();


# PACKET PROCESSING

    /**
     * zero fills all those packets that are shorter than  3 in length
     * @param $challenge_packet - the packet that requires to be zero filled
     * @return string $new_str - the zero filled,(left padded) packet
     */
    private final function zeroFiller($challenge_packet)
    {// N2wReaders::zeroFiller();
        $new_str = "";
        if(strlen($challenge_packet) === 2){
            $new_str = "0".$challenge_packet;
        }else{
            $new_str = "00".$challenge_packet;
        }

        return $new_str;
    } // N2wReaders::zeroFiller();


    /**
     * buildts a standard 3 charactered packets
     * @param $challenge_packet
     * @return string $_length_3_packet -  3 character string
     */
    private final function buildStandard3LengthPacket($challenge_packet)
    { // N2wReaders::buildStandard3LengthPacket();
        $_length_3_packet = "";
        if(strlen($challenge_packet) !== 3){
            $_length_3_packet = $this->zeroFiller($challenge_packet);
        }else{
            $_length_3_packet = $challenge_packet;
        }
        return $_length_3_packet;
    } // N2wReaders::buildStandard3LengthPacket();


    /**
     * define the a schema that will be used to read this packet.
     * @param string $challenge_packet - a string that represents a characteristic's packet to be read
     * @return string $_packet_schema - a string that represents the correct schema to be used to read this packet.
     */
    public function getPacketSchema($challenge_packet)
    {// N2wReaders::getPacketSchema();

        $_length_3_packet = "";
        $_packet_schema ="";
        if(strlen($challenge_packet) !== 3){
            $_length_3_packet = $this->zeroFiller($challenge_packet);
        }else{
            $_length_3_packet = $challenge_packet;
        }

        /**
         * get the atomic ones numeric values, from the packet and assign them to the packet
         * i.e
         * 789 , a->7 , b -> 8 , c -> 9
         */
        $a = substr($_length_3_packet,0,1);
        $b = substr($_length_3_packet,1,1);
        $c = substr($_length_3_packet,2,1);

        // resolving the schema for the three tokens, and pack them into a packet_schema
        $_packet_schema .= $this->resolveSchemataToken($a);
        $_packet_schema .= $this->resolveSchemataToken($b);
        $_packet_schema .= $this->resolveSchemataToken($c);

        return $_packet_schema;

    } //  N2wReaders::getPacketSchema();




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
     * reads the first right most value of the packet ( i.e abc in 789, c = 9),
     * the ones place value of a tri-character packet
     * @param $challenge_packet
     * @return bool|null|string
     */
    public function getC($challenge_packet)
    { // N2wReaders::getC();
        $c = null;
        if(strlen($challenge_packet) !== 3){
            exit('invalid C String');
        }else{
            $c = substr($challenge_packet,2,1);
        }
        return $c;
    } // N2wReaders::getC();

#PACKET SCHEMATA

    /**
     * @param $challenge_packet - the challenge packet string
     * @return string $packet_spelling - str of words representing the packet value
     */
    public final function ZZZSchemaReader($challenge_packet)
    { // N2wReaders::ZZZSchemaReader();
        $packet_spelling = "";
        return $packet_spelling;
    }  // N2wReaders::ZZZSchemaReader();


    public final function ZZNSchemaReader($challenge_packet)
    {
        $c = $this->getC($challenge_packet);
        $words = $this->ones[(int)$c];
        return $words;
        //return "";
    }




} // N2wReaders . close