<?php
/**
 * Created by PhpStorm.
 * User: vh1
 * Date: 6/19/17
 * Time: 9:37 PM
 */

namespace chitwarnold\n2w\en;


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
}