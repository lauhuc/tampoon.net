<?php namespace ajax;

header('Content-type: text/html; charset="UTF-8";');

use Manager\DatabaseManager;
use Manager\UtilitiesManager;

if(isset($_POST) && count($_POST) > 0)
{
    session_start();

    $a_cleaned_values = UtilitiesManager::checkEmptyDatasPost($_POST);

    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    if(is_array($a_cleaned_values))
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

            }else echo $output;
        }

    }else echo INPUTS_MANDATORIES;
}