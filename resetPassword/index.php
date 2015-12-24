<?php namespace resetPassword;

if(count($_GET) > 0)
{
    session_start();
    include_once '../translations/label_'.$_SESSION['locale'].'.php';

    $redis = new \Redis;
    $redis->connect('127.0.0.1');

    if($redis->get(trim($_GET['email'])) === trim($_GET['hash'])) //means user comes from email + has redis values existing
    {
?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>Tampoon</title>
<link rel="icon" href="../img/favicon.ico" />
<meta name="description" content="Tampoon" />
<link rel="stylesheet" type="text/css" href="../css/login.css"/>
    <script type="text/javascript" src="../js/translations.js"></script>
<script type="text/javascript" src="../js/script.js"></script>
</head>
<body>
<div id="main">
    it works!
</div>
</body>
</html>
<?php
    }else echo RESET_PASSWD_LINK_EXPIRED;
}