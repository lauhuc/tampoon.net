<?php namespace ajax;

header('Content-type: text/html; charset="UTF-8";');

use Manager\DatabaseManager;

session_start();

if(count($_POST) > 0)
{
    $isEmptyField = FALSE;

    foreach($_POST as $k => $v):

        $cleanedValue = trim($v);

        if(!empty($cleanedValue))
        {
            $a [$k] = $cleanedValue;

        }else{
            $isEmptyField = TRUE;
            BREAK;
        }

    endforeach;

    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    if(!$isEmptyField)
    {
        require_once '../Model/InitConsts.php';
        require_once '../Manager/DatabaseManager.php';

        $dbm = new DatabaseManager;

        $outputDBM = $dbm->updateUserPassword($_POST);

        if(is_bool($outputDBM))
        {
            $sucessMsg = PASSWORD_UPDATED;

        }else $errorMsg = $outputDBM;

    }else $errorMsg = INPUTS_MANDATORIES;

    echo (isset($errorMsg)) ? 'e<font color="red">'.$errorMsg.'</font>' : '<font color="green">'.$sucessMsg.'</font>';
}