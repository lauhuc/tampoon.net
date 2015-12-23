<?php namespace ajax;

use Manager\DatabaseManager;
use Manager\MailManager;
use Model\InitConsts;

header('Content-type: text/html; charset="UTF-8";');

if(count($_POST) > 0)
{
    $rescueEmail = trim($_POST['email_rescue']);

    if(strlen($rescueEmail) > 5)
    {
        session_start();
        include_once '../translations/label_'.$_SESSION['locale'].'.php';

        require_once '../Model/InitConsts.php';
        require_once '../Manager/DatabaseManager.php';

        $dm = new DatabaseManager;
        $outputdm = $dm->fetchUser($rescueEmail);

        if(is_bool($outputdm))
        {
            require_once '../Manager/MailManager.php';

            $msg = '<html><body><br><a href="http://tampoon.net/resetPassword/?hash=somethingdynamic">'.CLICK_TO_RESET_PASSWD.'</a>';
            $msg .= '<br>'.COPY_RESET_PASSWD_URL;
            $msg .= '<br>http://tampoon.net/resetPassword/?hash=somethingdynamic';
            $msg .= '</body></html>';

            $mm = new MailManager($rescueEmail, InitConsts::GMAIL_BOX, RESET_PASSWORD, $msg);
            $outputmm = $mm->send();

            if(is_bool($outputmm))
            {
                echo MAILS_SENT;

            }else $errorMsg = $outputmm;

        }else $errorMsg = UNEXISTING_EMAIL;
    }

    if(isset($errorMsg)) echo 'e<font color="red">'.$errorMsg.'</font>';
}