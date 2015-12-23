<?php namespace ajax;

header('Content-type: text/html; charset="UTF-8";');

use Model\InitConsts as IC;

session_start();

require_once '../Model/InitConsts.php';  //ENTRY POINT of execution => first class to be called then no need to require again IC

if(count($_POST) > 0)
{
    foreach($_POST as $k => $v):

        $cleanedValues = trim($v);

        if(!empty($cleanedValues)) $datasPost [$k] = $cleanedValues;

    endforeach;

    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    if(!empty($datasPost['clientEmail']) && !empty($datasPost['password']))
    {
        $errorMsg = '';
        include_once '../Manager/DatabaseManager.php';

        $dbm = new \Manager\DatabaseManager;
        $correctUser = $dbm->fetchUser($datasPost['clientEmail'], $datasPost['password']);

        if(is_bool($correctUser))
        {
            include_once '../Manager/FileManager.php';

            $fm = new \Manager\FileManager($datasPost['clientEmail'], $dbm->dateOrder);
            $outputCSV = $fm->formatAndWriteCSV($datasPost);

            if(is_string($outputCSV)) $errorMsg .= $outputCSV.'<br>';

            $outputPDF = $fm->formatAndWritePDF($datasPost);

            if(is_string($outputPDF)) $errorMsg .= $outputPDF.'<br>';

            $savedOrder = $dbm->saveOrder($datasPost, ($outputPDF && $outputCSV));

            if(is_string($savedOrder)) $errorMsg .= $savedOrder.'<br>';

            if(IC::SEND_MAIL_ENABLED)
            {
                include_once '../Manager/MailManager.php';
                $subject = PURCHASE_ORDER.' at tampoon.net';
                $msg = '<html><body><h3>Ref: '.$datasPost['clientEmail'].' '.$dbm->dateOrder.'</h3></body></html>';

                $mm = new \Manager\MailManager($datasPost['clientEmail'], IC::SENDER_NAME, $subject, $msg, [$fm->pdfPath, ]);
                $output = $mm->send();

                if(is_string($output)) $errorMsg .= $output;
                
                $mm2 = new \Manager\MailManager(IC::GMAIL_BOX, IC::SENDER_NAME, $subject, $msg, [$fm->pdfPath, $fm->csvPath, ]);
                $output2 = $mm2->send();

                if($output2)
                {
                    if(is_file($fm->csvPath)) unlink($fm->csvPath);

                    if(is_file($fm->pdfPath)) unlink($fm->pdfPath);

                    echo '<font color="green">'.MAILS_SENT.'</font>';

                }else $errorMsg .= $output;

            }else $errorMsg .= ENABLE_MAIL;

        }else $errorMsg = $correctUser;

    }else $errorMsg = INPUTS_MANDATORIES;

    if(!empty($errorMsg)) echo 'e<font color="red">'.$errorMsg.'</font>';
}