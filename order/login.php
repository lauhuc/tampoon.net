<?php namespace Order;

use Manager\DatabaseManager;
use Model\InitConsts;

if(count($_POST) > 0)
{
    session_start();
    var_dump($_POST);

    die;

    $b_for_check_empty = TRUE;

    foreach($_POST as $s_key => $s_value):

        $s_strip_spaces = trim($s_value);

        if(empty($s_strip_spaces))
        {
            $b_for_check_empty = FALSE;
            BREAK;

        }else $a_cleaned_values [$s_key] = $s_strip_spaces;

    endforeach;

    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    if($b_for_check_empty)
    {
        require_once '../Model/InitConsts.php';

        if($a_cleaned_values['psk'] === InitConsts::HASH_PASSWD)
        {
            require_once '../Manager/DatabaseManager.php';

            $mm = new DatabaseManager;

            $output = $mm->updatePasswdAndlogin($a_cleaned_values);

            if(is_bool($output))
            {
                $_SESSION['customer'] = $a_cleaned_values['email'];
                header('Location: ./');

            }else echo $output;

        }else echo CORRECT_PSK;

    }else echo INPUTS_MANDATORIES;

}