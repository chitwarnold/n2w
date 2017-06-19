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
     * decimal separator
     */
    protected $decimal_separator = ".";

    /**
     * thousands separator
     */
    protected $thousands_separator = ",";

    /**
     * decimal name
     */
    protected $decimal_separator_name = "point";

    /**
     * characteristic of challenge (the left side of the decimal)
     */
    private $characteristic = "";

    /**
     * mantisa of challenge (the left side of the decimal)
     */
    private $mantisa = "";
    /**
     * challenge packets ( available packets on this challenge)
     */
    private $challenge_packets = [];
    /**
     * solutions packets - store the packets that are used to spell the number.
     */
    private $solution_packets = [];


    /**
     * packet schema placeholder for 0(zero)
     */
    const PACKET_SCHEMA_PLACEHOLDER_ZERO = "Z";
    /**
     * packet schema placeholder for non-zero numbers
     */
    const PACKET_SCHEMA_PLACEHOLDER_NON_ZERO_NUMBER = "N";
    /**
     * challenge packet schema ZZZ ( 000 )
     */
    const CHALLENGE_PACKET_SCHEMA_ZZZ = 'ZZZ';
    /**
     * challenge packet schema ZZZ ( 001: (a=>0,b=>0,c=>[1-9]) )
     */
    const CHALLENGE_PACKET_SCHEMA_ZZN = 'ZZN';
    /**
     * challenge packet schema ZNZ ( 010: (a=>0,b=>[1-9],c=>0) )
     * 010,020,030,040,050,060,070,080,090,
     */
    const CHALLENGE_PACKET_SCHEMA_ZNZ = 'ZNZ';
    /**
     * challenge packet schema ZNN ( 019: (a=>0,b=>[1-9],c=>[1-9]) )
     * 099,015,070  usual two digit, teens and tens
     */
    const CHALLENGE_PACKET_SCHEMA_ZNN = 'ZNN';
    /**
     * challenge packet schema NNN ( 019: (a=>[1-9],b=>[1-9],c=>[1-9]) )
     * 789  usual three digit, teens and tens
     */
    const CHALLENGE_PACKET_SCHEMA_NNN = 'NNN';
    /**
     * challenge packet schema NNZ ( 770: (a=>[1-9],b=>[1-9],c=>0) )
     * 880   hundreds and tens
     */
    const CHALLENGE_PACKET_SCHEMA_NNZ = 'NNZ';
    /**
     * challenge packet schema NZZ ( 770: (a=>[1-9],b=>0,c=>0) )
     * 700   hundreds and tens
     */
    const CHALLENGE_PACKET_SCHEMA_NZZ = 'NZZ';
    /**
     * challenge packet schema NZN ( 707: (a=>[1-9],b=>0,c=>[1-9]) )
     * 901   hundreds and  ones
     */
    const CHALLENGE_PACKET_SCHEMA_NZN = 'NZN';



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
     * cleans the input that its given, by giving it the desired decimal points and then buiding a characteristic and mantisa
     * @param $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param integer $desired_decimal_points - desired number of decimal places
     * @return void
     */
    private final function sanitizeChallenge($challenge,$desired_decimal_points=2)
    { // N2w::sanitizeChallenge();
        $new_value =  number_format($challenge,$desired_decimal_points);
        $_char_mantisa = explode($this->decimal_separator, (string)$new_value);
        $this->characteristic = $_char_mantisa[0];
        $this->mantisa = $_char_mantisa[1];
    } // N2w::sanitizeChallenge();


    /**
     * generates the packets array for the  characteristic of the this object
     * allowing us to  know the place value character width  for the charactericts
     * @return void
     */
    public function genPackets()
    { // N2w::genPackets();

        $_packets = explode($this->thousands_separator, $this->characteristic);
        $this->challenge_packets['raw_packets_r'] = $_packets;
        $_packets_r = array_reverse($_packets);
        // build the dynamic schema of challenge_packets
        for($i=0; $i<count($_packets); $i++){
            $this->challenge_packets['refined_packets_r'][$this->class_multipliers[$i]] = $_packets_r[$i];
        }

    } // N2w::genPackets();

    /**
     * iterates throught the challenge packets creating a solution packet
     * allowing us to resolve the packet schemata,the class)name, the words,
     *  and to check the success of the operation
     * @return void
     */
    public function solveChallengePackets()
    {//N2w::solveChallengePackets();
        /**
         *  take in the key and value for each,
         *  read create schemas
         *  call approriate methods
         *  then get the strings
         *
         */

        foreach($this->challenge_packets['refined_packets_r'] as $key=>$val){
            //echo "<br /> $key => $val";
            $this->solution_packets[$key]['challenge'] = $val;
            $this->solution_packets[$key]['class_name'] = $key;
            $this->solution_packets[$key]['schema'] = $this->getPacketSchema($val);
            $this->solution_packets[$key]['words'] = $this->announcePacket($val);
            $this->solution_packets[$key]['resolved'] = ($this->announcePacket($val))? true:false;

        }


    } // N2w::solveChallengePackets();

    /**
     * removes the buggy "and on some strings"
     * @param integer $challenge - start position of and concatenator, where to begin the clean up from.
     * @return  string $remaining - the modulus string after removing the And concatenation
     */
    private function eliminateAnd($challenge)
    { // N2w::eliminateAnd();
        $and_pos = strpos($challenge, 'and',0);

        $remaining = "";

        if($and_pos){
            $remaining = substr($challenge, 4);
        }else{
            $remaining = $challenge;
        }

        return $remaining;

    } // N2w::eliminateAnd();

    /**
     * resolves packet schemata :
     * allowing for the resolving of the schemata of a packet, by going through each character in the packet
     * @param str $challenge_token - a string Token to be resolved to a schema value . i.e all (int)0 => Z and the rest of the numbers resolve to Z
     * @return str $token - a string token for the   given value
     * @todo implement the Exception Class instead of the exit;
     */
    public function resolveSchemataToken($challenge_token)
    { // N2w::resolveSchemataToken();
        $token = "";
        if(is_numeric($challenge_token) && (int)$challenge_token === 0){
            $token = self::PACKET_SCHEMA_PLACEHOLDER_ZERO;
        }else if(is_numeric($challenge_token) && (int)$challenge_token !== 0  ){
            $token = self::PACKET_SCHEMA_PLACEHOLDER_NON_ZERO_NUMBER;
        }else{
            exit('you must provide a numeric value to proceed');
        }
        return  $token;
    } // N2w::resolveSchemataToken();







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

    /**
     * NZNSchemaReader - called when a $challenge_packet looks like 707 (a,b,c)a=>[1-9],b=>0,c=>[1-9])
     * this is used to resolve tens
     * @param $challenge_packet - the challenge packet string
     * @return string -  string of words representing the packet value
     */
    public final function NZNSchemaReader($challenge_packet)
    { // N2wReaders::NZNSchemaReader();
        $c = $this->getC($challenge_packet);
        $b = $this->getB($challenge_packet);
        $a = $this->getA($challenge_packet);
        $z = "0";
        $ab = $a.$b;
        $bc = $b.$c;
        $words = null;

        // means its an ordinary two digit
        $once_words = $this->ones[(int)$c];
        $hundred_words = $this->hundreds[(int)$a.$z.$z];
        $words = $hundred_words." and ".$once_words;

        //echo $words;
        return $words ;
    } // N2wReaders::NZNSchemaReader();

    /**
     * ZNZSchemaReader - called when a $challenge_packet looks like 070 (a,b,c) (a=>0,b=>[1-9],c=>0)
     * this is used to resolve tens
     * @param $challenge_packet - the challenge packet string
     * @return string -  string of words representing the packet value
     */
    public final function ZNZSchemaReader($challenge_packet)
    { // N2wReaders::ZNZSchemaReader();
        $c = $this->getC($challenge_packet);
        $b = $this->getB($challenge_packet);
        $a = $this->getA($challenge_packet);
        $z = "0";
        $ab = $a.$b;
        $bc = $b.$c;
        $words = null;

        $words = $this->tens[(int)$bc];

        //echo $words;
        return $words ;
    } // N2wReaders::ZNZSchemaReader();

    /**
     * ZNZSchemaReader - called when a $challenge_packet looks like 070 (a,b,c) (a=>0,b=>[1-9],c=>0)
     * this is used to resolve tens
     * @param $challenge_packet - the challenge packet string
     * @return string -  string of words representing the packet value
     */
    public final function NZZSchemaReader($challenge_packet)
    { // N2wReaders::NZZSchemaReader();
        $c = $this->getC($challenge_packet);
        $b = $this->getB($challenge_packet);
        $a = $this->getA($challenge_packet);
        $z = "0";
        $ab = $a.$b;
        $bc = $b.$c;
        $words = null;
        $words = $this->hundreds[(int)$a.$z.$z];

        //echo $words;
        return $words ;
    } // N2wReaders::NZZSchemaReader();


#WORKHORSE
    /**
     *  takes any number determine its schema, and make  the call to the correct reader
     * and announce the name(christen the challenge packet) of the packet. This name is without the class multipliers.
     *
     * @param string $challenge_packet - the packet to be spelled
     * @return mixed|string $packet_name - the words version of the challenge packet. the spelling of the packet
     */
    private function announcePacket($challenge_packet){
        // schemata of interest
        $schema = $this->getPacketSchema($challenge_packet);
        $packet_name = "";

        // normalize the packet
        $challenge_packet = $this->buildStandard3LengthPacket($challenge_packet);

        switch((string)$schema){
            // 000, ZZZ
            case self::CHALLENGE_PACKET_SCHEMA_ZZZ:
                $packet_name = $this->ZZZSchemaReader($challenge_packet);
                break;
            // 001  ones, 'ZZN'
            case self::CHALLENGE_PACKET_SCHEMA_ZZN:
                $packet_name = $this->ZZNSchemaReader($challenge_packet);
                break;
            // 010,020,030,040,050,060,070,080,090, 'ZNZ'
            case self::CHALLENGE_PACKET_SCHEMA_ZNZ:
                $packet_name = $this->ZNZSchemaReader($challenge_packet);
                break;
            // 099,015,070  usual two digit, teens and tens ,'ZNN'
            case self::CHALLENGE_PACKET_SCHEMA_ZNN:
                $packet_name = $this->ZNNSchemaReader($challenge_packet);
                break;
            // 789 usual 3 digit non zero anywhere packet,'NNN'
            case self::CHALLENGE_PACKET_SCHEMA_NNN:
                $packet_name = $this->NNNSchemaReader($challenge_packet);
                break;
            // 770 , hundreds and tens , 'NNZ'
            case self::CHALLENGE_PACKET_SCHEMA_NNZ :
                $packet_name = $this->NNZSchemaReader($challenge_packet);
                break;
            // 700 , hundreds only
            case self::CHALLENGE_PACKET_SCHEMA_NZZ:
                $packet_name = $this->NZZSchemaReader($challenge_packet);
                break;
            // 707 , hundreds and ones
            default :
                // NZN
                $packet_name = $this->NZNSchemaReader($challenge_packet);
                break;

        } // close switch


        //echo $packet_name;
        return $packet_name;


    } // announcePacket

# SPEAK

    /**
     * allows us to read multi packeted strings not just one packet (like when calling Announce)
     * @param $challenge
     * @param $decimal_places
     */

    /**
     * @param string $challenge - the strings that represents the number challenge to be spelled
     * @param  integer $decimal_places - the decimal numbers to consider
     * @return string $words - the English words for the application
     */
    public function spell($challenge,$decimal_places)
    { // N2wReaders::spell();
        $words ="";
        $this->sanitizeChallenge($challenge,$decimal_places);
        $this->genPackets();
        $this->solveChallengePackets();

        // helps with adding "ands"
        $cursor = 0;

        switch(count($this->solution_packets)){
            case 0 :
                $words .= "";
                break;
            default:
                $solution_reversed_array = array_reverse($this->solution_packets);
                foreach($solution_reversed_array as $resolved_packet){
                    $class_name = $resolved_packet['class_name'];
                    $resolved = $resolved_packet['resolved'];
                    $challenge = $resolved_packet['challenge'];
                    $challenge = $this->buildStandard3LengthPacket($challenge);
                    $cursor++;
                    $resolve_value = $resolved_packet['words'];
                    $words;
                    if($resolved && ((int)$resolved_packet['words'] !== 0 || $resolved_packet['words'] !== "") ){

                        if($cursor === (count($solution_reversed_array)) && ((int)$challenge < 99) && (count($solution_reversed_array) > 1)) {
                            // attracting  "the hundreds class_name preceeding and " at he right place,
                            // 1005 => one thousand and five
                            // 1500 => one thousand five hundred not  one thousand and  five hundred
                            //$words .= " and $challenge ";
                            $words .= " and ";
                        }

                        $words .= $resolved_packet['words'];

                    }

                    // add the class name
                    // avoing the 1,000,000, being reported as "one million and thousand and seven hundred"
                    // also avoiding 000-999 to be reported as {packet+value}+ "hundred"

                    if($class_name !== 'hundred' && ((int)$resolved_packet['words'] !== 0 || $resolved_packet['words'] !== "")  ){
                        $words .= " $class_name,";
                    }




                    // and addition if the
                    // and we are not on a value of empty string or zero
                    // if($cursor !== (count($solution_reversed_array) -1) && ((int)$resolved_packet['words'] !== 0 || $resolved_packet['words'] !== "") ){
                    // $words .= " and ";
                    // }


                }


                break;



        }

        //echo $this->eliminateAnd($words);
        return $words;


    } // N2wReaders::spell();



} // N2wReaders . close