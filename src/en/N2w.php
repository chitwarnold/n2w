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
     * @var the challenge for the day
     */
    public $challenge;
    /**
     * decimal point to be be allocated
     */
    public $desired_decimal_points  = 2;

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



} // N2w : close class