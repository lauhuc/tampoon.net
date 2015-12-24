<?php namespace ajax;

header('Content-type: text/html; charset="UTF-8";');

use Manager\DatabaseManager;
use Manager\FileManager;
use Manager\MailManager;
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
    
    $errorMsg = '';
    
    include_once '../Manager/DatabaseManager.php';
    $dbm = new DatabaseManager;

    include_once '../Manager/FileManager.php';
    $fm = new FileManager($_SESSION['customer_email'], $dbm->dateOrder);
    $outputCSV = $fm->formatAndWriteCSV($datasPost);

    if(is_string($outputCSV)) $errorMsg .= $outputCSV.'<br>';

    $outputPDF = $fm->formatAndWritePDF($datasPost, $_SESSION['customer_email']);

    if(is_string($outputPDF)) $errorMsg .= $outputPDF.'<br>';

    $savedOrder = $dbm->saveOrder($datasPost, (is_bool($outputPDF) && is_bool($outputCSV)), $_SESSION['customer_id']);

    if(is_string($savedOrder)) $errorMsg .= $savedOrder.'<br>';

    if(IC::SEND_MAIL_ENABLED)
    {
        include_once '../Manager/MailManager.php';
        $subject = PURCHASE_ORDER.' tampoon.net';
        $msg = '<html><body><h3>Ref: '.$_SESSION['customer_email'].' '.$dbm->dateOrder.'</h3></body></html>';

        $mm = new MailManager($_SESSION['customer_email'], IC::SENDER_NAME, $subject, $msg, [$fm->pdfPath, ]);
        $output = $mm->send();

        if(is_string($output)) $errorMsg .= $output;
                
        $mm2 = new MailManager(IC::GMAIL_BOX, IC::SENDER_NAME, $subject, $msg, [$fm->pdfPath, $fm->csvPath, ]);
        $output2 = $mm2->send();

        if($output2)
        {
            if(is_file($fm->csvPath)) unlink($fm->csvPath);

            if(is_file($fm->pdfPath)) unlink($fm->pdfPath);

            echo '<font color="green">'.MAILS_SENT.'</font>';

        }else $errorMsg .= $output;

    }else $errorMsg .= ENABLE_MAIL;
    
    if(!empty($errorMsg)) echo 'e<font color="red">'.$errorMsg.'</font>';
}