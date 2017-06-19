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
     * the ones values of the arabic numerals
     * allowing us to spell out the numbers in the ones place value positions.
     */
    private $ones = [
        0=>'',
        1=>'one',
        2=>'two',
        3=>'three',
        4=>'four',
        5=>'five',
        6=>'six',
        7=>'seven',
        8=>'eight',
        9=>'nine'
    ];


    /**
     * the teens values of the arabic numerals
     * allowing us to spell out the numbers in the tens place value positions, between 11 and 19.
     */
    private $teens = [
        11=>'eleven',
        12=>'twelve',
        13=>'thirteen',
        14=>'fourteen',
        15=>'fifteen',
        16=>'sixteen',
        17=>'seventeen',
        18=>'eighteen',
        19=>'nineteen'
    ];

    /**
     * the tens values of the arabic numerals
     * allowing us to spell out the numbers in the tens place value positions, factors of ten close
     */
    private $tens = [
        10=>'ten',
        20=>'twenty',
        30=>'thirty',
        40=>'fourty',
        50=>'fifty',
        60=>'sixty',
        70=>'seventy',
        80=>'eighty',
        90=>'ninety'
    ];

    /**
     * the hundreds values of the arabic numerals
     * allowing us to spell out the numbers in the hundreds place value positions, factors of 100 close
     */
    private $hundreds = [
        100=>'one hundred',
        200=>'two hundred',
        300=>'three hundred',
        400=>'four hundred',
        500=>'five hundred',
        600=>'six hundred',
        700=>'seven hundred',
        800=>'eight hundred',
        900=>'nine hundred'
    ];

    /**
     * class multipliers
     * @link https://en.wikipedia.org/wiki/Names_of_large_numbers
     */
    private $class_multipliers = array(
        0=>'hundred', // 100 - 999 [ covers numbers with 1 packet]
        1=>'thousand', // 100,000 - 999,000 [ covers numbers with 2 packet]
        2=>'million', // 1,000,000 - 999,000,000 [ covers numbers with 3 packet]
        3=> 'billion', // 1,000,000,000 - 999,999,999,999 [ covers numbers with 4 packet]
        4=> 'trillion', // 1,000,000,000,000 - 999,999,999,999,999 [ covers numbers with 5 packet]
        5=> 'quadrillion',
        6=> 'quintillion'
    );


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
     * ZZZSchemaReader - reads schemata that is like 000 and resolves the words and returns that words
     * called when a $challenge_packet looks like 000
     * @param $challenge_packet - the challenge packet string
     * @return string $packet_spelling - str of words representing the packet value
     */
    public final function ZZZSchemaReader($challenge_packet)
    { // N2wReaders::ZZZSchemaReader();
        $packet_spelling = "";
        return $packet_spelling;
    }  // N2wReaders::ZZZSchemaReader();

    /**
     * ZZNSchemaReader -  called when a $challenge_packet looks like 001 to 009 (a=>0,b=>0,c=>9)
     *  this is used to resolve the ones
     * @param $challenge_packet - the challenge packet string
     * @return mixed
     */
    public final function ZZNSchemaReader($challenge_packet)
    { // N2wReaders::ZZNSchemaReader();
        $c = $this->getC($challenge_packet);
        $words = $this->ones[(int)$c];
        return $words;
        //return "";
    } // N2wReaders::ZZNSchemaReader();

    /**
     * ZNNSchemaReader -  called when a $challenge_packet looks like 061,011,060 (a=>0,b=>[0-9],c=>[0-9])
     * this is used to resolve the tens,teens and the rest of double digits
     * @param $challenge_packet - the challenge packet string
     * @return string $words -  string of words representing the packet value
     */
    public final function ZNNSchemaReader($challenge_packet)
    {// N2wReaders::ZZNSchemaReader();
        $c = $this->getC($challenge_packet);
        $b = $this->getB($challenge_packet);
        $a = $this->getA($challenge_packet);
        $z = "0";
        $ab = $a.$b;
        $bc = $b.$c;
        $words = null;
        // resolve tens
        if((int)$a ===0 && (int)$c === 0){
            $words = $this->tens[(int)$bc];
        }else if((int)$a ===0 && (int)$b === 1){
            // solve teens
            $words = $this->teens[(int)$bc];
        }else{
            // means its an ordinary two digit
            $once_words = $this->ones[(int)$c];
            $ten_words = $this->tens[(int)$b.$z];
            $words = $ten_words." ".$once_words;
        }

        return $words ;

    } // N2wReaders::ZZNSchemaReader();

    /**
     * NNNSchemaReader -  called when a $challenge_packet looks like 789 (a,b,c)(a=>[1-9],b=>[1-9],c=>[1-9])
     * this is used to resolve the tens,teens and the rest of double digits
     * @param $challenge_packet - the challenge packet string
     * @return string $words -  string of words representing the packet value
     */
    public final function NNNSchemaReader($challenge_packet)
    { //N2wReaders::NNNSchemaReader();
        $c = $this->getC($challenge_packet);
        $b = $this->getB($challenge_packet);
        $a = $this->getA($challenge_packet);
        $z = "0";
        $words = null;

        // means its an ordinary two digit
        $once_words = $this->ones[(int)$c];
        $ten_words = $this->tens[(int)$b.$z];
        $hundred_words = $this->hundreds[(int)$a.$z.$z];
        $words = $hundred_words." and ".$ten_words." ".$once_words;



        //echo $words;
        return $words ;
    } //  N2wReaders::NNNSchemaReader();

    /**
     * NNZSchemaReader -  called when a $challenge_packet looks like 770 (a,b,c)(a=>[1-9],b=>[1-9],c=>0)
     * this is used to resolve triple digits with zero at c on the string
     * @param $challenge_packet - the challenge packet string
     * @return string -  string of words representing the packet value
     */
    public final function NNZSchemaReader($challenge_packet)
    { // N2wReaders::NNZSchemaReader();
        $c = $this->getC($challenge_packet);
        $b = $this->getB($challenge_packet);
        $a = $this->getA($challenge_packet);
        $z = "0";
        $ab = $a.$b;
        $bc = $b.$c;
        $words = null;

        // means its an ordinary two digit

        $ten_words = $this->tens[(int)$bc];
        $hundred_words = $this->hundreds[(int)$a.$z.$z];
        $words = $hundred_words." and ".$ten_words;

        //echo $words;
        return $words ;
    } // N2wReaders::NNZSchemaReader();


} // N2wReaders . close