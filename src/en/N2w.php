<?php
/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/19/17
 * Time: 9:37 PM
 */

namespace chitwarnold\n2w\en;

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
     * @var the challenge for the day
     */
    private $_challenge;
    /**
     * decimal point to be be allocated
     */
    private $_desired_decimal_points  = 2;
    /**
     * spelling returned
     */
    private $_spelling = "";

    /**
     * the active reader class that is use for the class
     * is an instance of  N2wReaders
     */
    private $_reader;


    /**
     * N2w constructor., assumes that native reader will be used and just calls it.
     */
    public function __construct()
    { // N2w::__construct();
        // sets a reader
        $_reader = new N2wReaders();
        $this->setReader($_reader);

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
     * statically spells everything without all the set up
     *  will automatically call the {languages} default reader -- en
     *
     * @param numeric $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param int $desired_decimal_points - desired number of decimal places
     * @return array - of the artifacts - the words that spell the number challenge
     * @todo
     */
    public  function spellingBee()
    { // N2w::spellingBee();
        return [
            'challenge' => $this->_challenge,
            'dp' => $this->_desired_decimal_points,
            'solution' => $this->getSpelling(),
            'spelling' => $this->_spelling,
            'rd_dump' => $this->_reader->getVars(),
            'reader_value' => $this->_reader->spell( $this->_challenge,$this->_desired_decimal_points)
        ];

    } // N2w::spellingBee();



    /**
     * n2w factory method
     * @param N2wReaders $reader - the class that shall manage all the reading of the numbers
     * @return N2w $_n2w_obj - an object that is ready to spell numeric string
     */
    public final static function factory(N2wReaders $reader)
    { // N2w::factory();
        $_n2w_obj = new N2w();
        $_n2w_obj->setReader($reader);
        return $_n2w_obj;
    } // N2w::factory();


    /**
     * show off the  you can spell
     */
    private function getSpelling()
    { // N2w::getSpelling();
        // get the reader
        $_reader = $this->_reader;
        $this->_spelling = $_reader->spell($this->_challenge,$this->_desired_decimal_points);
        return $this->_spelling;
    } //  N2w::getSpelling();

    /**
     * solves and returns the number of the spelling
     */

    /**
     * @param numeric $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param int $desired_decimal_points - desired number of decimal places
     * @return string - the words that spell the number challenge
     */
     public function solve($challenge,$desired_decimal_points)
     { // N2w::solve();
         try {

             if (strlen($challenge) > 0 && is_numeric($challenge)) {
                 $this->_challenge = $challenge;
                 $this->_desired_decimal_points = $desired_decimal_points;
                 // return the spelling
                 return $this->getSpelling();

             } else {
                 throw  new N2wException('Challenge is not A Valid Value, - Not Numeric');
             }

         } catch (N2wException $e) {
             exit($e->getMessage());
         }

     } // N2w::solve();


    /**
     * api for updating the challenge and desired decimal properties values on this object
     * @param numeric $challenge - a string that can be cast to a floating point number  to be resolved to words
     * @param int $desired_decimal_points - desired number of decimal places
     * @return string - the words that spell the number challenge
     */
    public function updateChallenge($challenge,$desired_decimal_points)
    {// N2w::updateChallenge();
        $this->_challenge = $challenge;
        $this->_desired_decimal_points = $desired_decimal_points;
        return $this;
    } // N2w::updateChallenge();

    /**
     * allows us to spell the current number
     */
    public function spell()
    {//  N2w::spell();
        return $this->getSpelling();
    }// N2w::spell();



} // N2w : close class