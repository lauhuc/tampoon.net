<?php namespace ajax;

use Manager\DatabaseManager;

header('Content-type: text/html; charset="UTF-8";');

if(count($_POST) > 0)
{
    session_start();
    include_once '../translations/label_'.$_SESSION['locale'].'.php';
    require_once '../Model/InitConsts.php';
    require_once '../Manager/DatabaseManager.php';

    $dm = new DatabaseManager;
    $output = $dm->updateUserPassword(['password' => trim($_POST['password']), 'email' => $_POST['email'], ]);

    if(is_bool($output))
    {
        $userDatas = $dm->fetchUser($_POST['email'], trim($_POST['password']));

        if(is_array($userDatas))
        {
            $_SESSION['customer_email'] = $userDatas['email'];
            $_SESSION['customer_id'] = $userDatas['id'];

            echo '<a href="../order">'.CONNECTION.'</a>';

        }else $errorMsg = $userDatas;

    }else $errorMsg = $output;

    if(isset($errorMsg)) echo 'e<a href="#" onclick="updatePasswd();">'.UPDATE.'</a><br><font color="red">'.$errorMsg.'</font>';
}