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




}