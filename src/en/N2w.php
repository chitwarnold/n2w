<?php
/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/19/17
 * Time: 9:37 PM
 */

namespace chitwarnold\n2w\en;

use chitwarnold\n2w\en\N2wException;


/**
 *  This service allows for the  generation of english words that refer to numbers
 *   NB, this class does not provide for a way to read currency values and should not be confused for such, i.e
 *  it will read the values of a mantisa as single digit values i.e 10.25 as ten point two,five and not 10 Shilling and 25 cents
 *  incase the client coder intends to use it otherwise you are adviced to query the mantisa value separately as a number and then furnish the
 *  returned answer as payload.
 * @author Chitwa Arnold Astrill for Cymap-Gomersol Technologies
 * @notes  and algorithm on tine book
 *
 */

class N2w
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
     * N2w constructor.
     * @param $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param int $desired_decimal_points - desired number of decimal places
     */
    public function __construct($challenge,$desired_decimal_points=2)
    { // N2w::__construct();

        $this->sanitizeChallenge($challenge,$desired_decimal_points);

    } //  N2w::__construct();

    /**
     * cleans the input that its given, by giving it the desired decimal points and then buiding a characteristic and mantisa
     * @param $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param int $desired_decimal_points - desired number of decimal places
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
     * n2w factory method
     */
    /**
     * @param $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param int $desired_decimal_points - desired number of decimal places
     * @return N2w
     */
    public final static function factory($challenge,$desired_decimal_points=2)
    { // N2w::factory();
        return new N2w($challenge,$desired_decimal_points);
    } // N2w::factory();


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
     * zero fills all those packets that are shorter than  3 in length
     * @param $challenge_packet - the packet that requires to be zero filled
     * @return string $new_str - the zero filled,(left padded) packet
     */
    private final function zeroFiller($challenge_packet)
    {// N2w::zeroFiller();
        $new_str = "";
        if(strlen($challenge_packet) === 2){
            $new_str = "0".$challenge_packet;
        }else{
            $new_str = "00".$challenge_packet;
        }

        return $new_str;
    } // N2w::zeroFiller();


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
     * @param $challenge_packet
     * @return string
     */
    public function getPacketSchema($challenge_packet)
    {N2w::getPacketSchema();
        /**
         * 789 , a->7 , b -> 8 , c -> 9
         */


        $_length_3_packet;
        $a;
        $b;
        $c;
        $_packet_schema ="";
        if(strlen($challenge_packet) !== 3){
            $_length_3_packet = $this->zeroFiller($challenge_packet);
        }else{
            $_length_3_packet = $challenge_packet;
        }
        $a = substr($_length_3_packet,0,1);
        $b = substr($_length_3_packet,1,1);
        $c = substr($_length_3_packet,2,1);

        // resolving the schema for the three tokens
        $_packet_schema .= $this->resolveSchemataToken($a);
        $_packet_schema .= $this->resolveSchemataToken($b);
        $_packet_schema .= $this->resolveSchemataToken($c);

        //echo "<br /> a : $a , <br /> b : $b , <br /> c : $c , <br /> schema  : $_packet_schema";
        return $_packet_schema;


    } //  N2w::getPacketSchema();


} // N2w : close class