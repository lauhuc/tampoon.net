<?php
/**
 * Created by PhpStorm.
 * User: art
 * Date: 23/12/15
 * Time: 16:35
 */

namespace Manager;

use Model\InitConsts as IC;

class UtilitiesManager implements IC
{
    /**
     * @param array $datasPost
     * @return bool|string
     */
    public static function checkUserFirstLoginRequirement(array $datasPost)
    {
        if(sha1($datasPost['psk']) === IC::HASH_PASSWD) //one always check the hash value
        {
            if(strlen($datasPost['new_password']) > 5)
            {
                if(sha1($datasPost['new_password']) !== IC::HASH_PASSWD)// the new password cannot be as the PSK
                {
                    return TRUE;

                }else return DEFINE_NEW_PASSWD;

            }else return MIN_LEN_PASSWD;

        }else return CORRECT_PSK;
    }

    /**
     * @param array $datasPost
     * @return bool
     */
    public static function checkEmptyDatasPost(array $datasPost)
    {
        if(count($datasPost) === 0) return FALSE;

        foreach($datasPost as $s_key => $s_value):

            $s_strip_spaces = trim($s_value);

            if(empty($s_strip_spaces))
            {
                return FALSE;

            }else $a_cleaned_values [$s_key] = $s_strip_spaces;

        endforeach;

        return $a_cleaned_values;
    }
}