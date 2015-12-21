<?php

namespace ajax;

use Model\InitConsts as IC;

session_start();

require_once '../Model/InitConsts.php';  //ENTRY POINT of execution => first class to be called then no need to require again IC

if(count($_POST) > 0)
{

    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    $email      = trim($_POST['clientEmail']);
    $password   = trim($_POST['password']);

    if(!empty($email) && !empty($password))
    {
        include_once '../Manager/DatabaseManager.php';

        $dbm = new \Manager\DatabaseManager;
        $correctUser = $dbm->fetchUser($email, $password);

        if(is_bool($correctUser))
        {
            include_once '../Manager/FileManager.php';

            $fm = new \Manager\FileManager($email, $dbm->dateOrder);
            $outputCSV = $fm->formatAndWriteCSV($_POST);

            if(is_string($outputCSV)) $errorMsg = $outputCSV.'<br>';

            $outputPDF = $fm->formatAndWritePDF($_POST);

            if(is_string($outputPDF)) $errorMsg .= $outputPDF.'<br>';

            $savedOrder = $dbm->saveOrder($_POST, ($outputPDF && $outputCSV));

            if(is_string($savedOrder)) $errorMsg .= $savedOrder.'<br>';

            if(IC::SEND_MAIL_ENABLED)
            {
                include_once '../Manager/MailManager.php';

                $mm = new \Manager\MailManager($email, $fm->ref, [$fm->csvPath, $fm->pdfPath, ], $dbm->date);
                $output = $mm->send();

                if($output)
                {
                    if(is_file($fm->csvPath)) unlink($fm->csvPath);

                    if(is_file($fm->pdfPath)) unlink($fm->pdfPath);

                    echo '<font color="green">'.MAILS_SENT.'</font>';

                }else $errorMsg = $output;

            }else $errorMsg .= ENABLE_MAIL;

        }else $errorMsg = $correctUser;

    }else $errorMsg = INPUTS_MANDATORIES;

    echo 'e<font color="red">'.$errorMsg.'</font>';
}