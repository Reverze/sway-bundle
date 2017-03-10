<?php

namespace SwayBundle\Utils;


/**
 * Binary safe string comparison (case-sensitive). 
 * It uses 'strcmp' function.
 * @param string $string1 
 * @param string $string2
 * @return boolean Returns True or False, If string are equal returns True
 */
function string_equal(string $string1, string $string2)
{
    return (bool) (strcmp($string1, $string2) === 0);
}

/**
 * Binary safe string comparison (case-sensitive).
 * Ut uses 'strcmp' function
 * @param string $string1
 * @param string $string2
 * @return boolean Returns True or False. If string1 is less than string2 returns True
 */
function string_is_less(string $string1, string $string2)
{
    return (bool) (strcmp($string1, $string2) < 0 );
}

/**
 * Binary safe string comparison (case-sensitive).
 * @param string $string1
 * @param string $string2
 * @return boolean Returns True or False. If string1 is greater than string2 returns True
 */
function string_is_greater(string $string1, string $string2)
{
    return (bool) (strcmp($string1, $string2) > 0);
}

/**
 * Checks if string contains specified character or word
 * @param string $string
 * @param string $needle
 * @return boolean Returns True of False. If string contains needle returns True
 */
function string_has(string $string, string $needle)
{
    return (bool) (strpos($string, $needle) !== false);
}

?>

