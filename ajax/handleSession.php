<?php namespace ajax;

header('Content-type: text/html; charset="UTF-8";');

use Manager\DatabaseManager;

if(isset($_POST) && count($_POST) > 0)
{
    session_start();

    $b_for_chek_empty = TRUE;

    foreach($_POST as $s_key => $s_value):

        $s_strip_spaces = trim($s_value);

        if(empty($s_strip_spaces))
        {
            $b_for_chek_empty = FALSE;
            BREAK;

        }else $a_cleaned_values [$s_key] = $s_strip_spaces;

    endforeach;

    $errorMsg = '';

    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    if($b_for_chek_empty)
    {
        if(FALSE !== stripos($a_cleaned_values['email'], '@') && FALSE !== stripos($a_cleaned_values['email'], '.'))
        {
            require_once '../Model/InitConsts.php';
            require_once '../Manager/DatabaseManager.php';

            $mm = new DatabaseManager;

            $output = $mm->fetchUser($a_cleaned_values['email']);

            if(is_bool($output))
            {
                if($output)
                {
                    echo '<br><input type="password" name="password" placeholder="'.PASSWD.'">';
                    echo '<br><br><a href="#" onclick="document.getElementById(\'the_form\').submit();">'.CONNECTION.'</a>';

                }else
                {
                    echo '<input type="hidden" name="first_login" value="true">';
                    echo '<br><input type="password" name="psk" placeholder="'.PSK.'">';
                    echo '<br><input type="password" name="new_password" placeholder="'.NEW_PASSWD.'">';
                    echo '<br><br><a href="#" onclick="document.getElementById(\'the_form\').submit();">'.CONNECTION.'</a>';
                }

            }else $errorMsg .= $output;
        }

    }else $errorMsg = INPUTS_MANDATORIES;

    if(!empty($errorMsg)) echo 'e<font color="red">'.$errorMsg.'</font>';
}