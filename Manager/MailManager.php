<?php

/**
 * Created by PhpStorm.
 * User: art
 * Date: 15/12/15
 * Time: 11:47
 */
namespace Manager;

use Model\InitConsts as IC;

class MailManager implements IC
{
    /**
     * @var
     */
    private $PHPMailer;

    /**
     * MailManager constructor.
     * @param $p1_email
     * @param $p2_ref
     * @param array $p3_files_paths
     * @param $p4_date
     */
    public function __construct($p1_email, $p2_ref, array $p3_files_paths, $p4_date)
    {
        require '../vendor/autoload.php';

        $this->PHPMailer = new \PHPMailer;

        $this->PHPMailer->isSMTP();
//Enable SMTP debugging 0 = off (for production use) 1 = client messages 2 = client and server messages
        $this->PHPMailer->SMTPDebug    = 0;
        $this->PHPMailer->Debugoutput  = 'html';
        $this->PHPMailer->Host         = IC::SMTP;
        $this->PHPMailer->Port         = 587;
        $this->PHPMailer->SMTPSecure   = 'tls';
        $this->PHPMailer->SMTPAuth     = true;
//Username to use for SMTP authentication - use full email address for gmail
        $this->PHPMailer->Username     = IC::GMAIL_BOX;
//Password to use for SMTP authentication
        $this->PHPMailer->Password     = IC::GMAIL_PASSWORD;            //BE CAREFUL with the $ when using double quotes!!!
        $this->PHPMailer->setFrom(IC::SENDER, IC::SENDER_NAME);
        $this->PHPMailer->addReplyTo(IC::SENDER, IC::SENDER_NAME);
        $this->PHPMailer->addAddress($p1_email, strstr($p1_email, '@', TRUE));
        //$this->PHPMailer->addAddress(IC::GMAIL_BOX, IC::SENDER_NAME);
        $this->PHPMailer->Subject = 'Order Ref: '.$p2_ref;
        $this->PHPMailer->msgHTML('<html><head></head><body><h1>Tampoon order from '.$p1_email.' the '.$p4_date.'</h1></body></html>');

        foreach($p3_files_paths as $v) $this->PHPMailer->addAttachment($v);

    }

    /**
     * @return bool|string
     * @throws \phpmailerException
     */
    public function send()
    {
         if($this->PHPMailer->send())
         {
             return TRUE;

         }else return $this->PHPMailer->ErrorInfo;
    }
}