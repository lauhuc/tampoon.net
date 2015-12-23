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
     * @param $p3_sender
     * @param $p4_subject
     * @param $p5_msg
     * @param array $p6_files_paths
     */

    public function __construct($p1_email, $p3_sender, $p4_subject, $p5_msg, array $p6_files_paths = [])
    {
        require '../vendor/autoload.php';

        $this->PHPMailer = new \PHPMailer;
        $this->PHPMailer->CharSet = 'UTF-8';

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
        $this->PHPMailer->setFrom(IC::GMAIL_BOX, IC::SENDER_NAME);
        $this->PHPMailer->addReplyTo(IC::GMAIL_BOX, IC::SENDER_NAME);
        $this->PHPMailer->addAddress($p1_email, $p3_sender);
        $this->PHPMailer->Subject = $p4_subject;
        $this->PHPMailer->msgHTML($p5_msg);

        if(count($p6_files_paths) > 0)
        {
            foreach($p6_files_paths as $v) $this->PHPMailer->addAttachment($v);
        }
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