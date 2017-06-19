<?php
/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/19/17
 * Time: 9:37 PM
 */

namespace chitwarnold\n2w\en;

use chitwarnold\n2w\en\N2wException;
use chitwarnold\n2w\en\readers\N2wReaders;


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
     * packet schema placeholder for 0(zero)
     */
    const PACKET_SCHEMA_PLACEHOLDER_ZERO = "Z";
    /**
     * packet schema placeholder for non-zero numbers
     */
    const PACKET_SCHEMA_PLACEHOLDER_NON_ZERO_NUMBER = "N";
    /**
     * the active reader class that is use for the class
     * is an instance of  N2wReaders
     */
    private $_reader;











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
     * assigns the default packet reader class for this spelling class
     * @param N2wReaders $packet_reader_class
     * @return void;
     */
    public function setReader(N2wReaders $packet_reader_class)
    { // N2w::setReader();
        $this->_reader = $packet_reader_class;
    } // N2w::setReader();

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
     * n2w factory method
     * @param N2wReaders $reader
     * @param $challenge
     * @param integer $desired_decimal_points
     * @return N2w
     */
    public final static function factory(N2wReaders $reader,$challenge,$desired_decimal_points=2)
    { // N2w::factory();
        $_n2w_obj = new N2w($challenge,$desired_decimal_points);
        // assign the reader
        $_n2w_obj->setReader($reader);
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


} // N2w : close class