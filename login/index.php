<?php namespace login;

use Manager\DatabaseManager;
use Manager\UtilitiesManager;

session_start();
include_once '../translations/label_'.$_SESSION['locale'].'.php'; //entry const file translation

if(isset($_GET['do']) && trim($_GET['do']) === 'logout') unset($_SESSION['customer']);

if(count($_POST) > 0)
{
    require_once '../Model/InitConsts.php';
    require_once '../Manager/UtilitiesManager.php';

    $a_cleaned_values = UtilitiesManager::checkEmptyDatasPost($_POST);

    if(is_array($a_cleaned_values))
    {
        if(isset($_POST['first_login'])) //first login comes from an HIDDEN input
        {
            $firstLoginRequirements = UtilitiesManager::checkUserFirstLoginRequirement($a_cleaned_values);

            if(is_bool($firstLoginRequirements))
            {
                require_once '../Manager/DatabaseManager.php';
                $mm = new DatabaseManager;
                $output = $mm->updatePasswdAndlogin($a_cleaned_values);

                if(is_bool($output))
                {
                    $_SESSION['customer'] = $a_cleaned_values['email'];
                    header('Location: ../order');

                }else $errorMsg = $output;

            }else $errorMsg = $firstLoginRequirements;

        }else //most frequent scenario : when user already registered
        {
            require_once '../Manager/DatabaseManager.php';
            $mm = new DatabaseManager;
            $output = $mm->fetchUser($a_cleaned_values['email'], $a_cleaned_values['password']);

            if(is_bool($output))
            {
                $_SESSION['customer'] = $a_cleaned_values['email'];
                header('Location: ../order');

            }else $errorMsg = $output;
        }

    }else $errorMsg = INPUTS_MANDATORIES;
}
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
    <h1><?php echo CONNECTION ?></h1>
    <form method="post" name="the_form" id="the_form">
        <input type="email" name="email" placeholder="Email" onkeyup="handleSession(this.value);" value="<?php echo (!empty($_POST['email'])) ? $_POST['email'] : '' ?>"><br>
        <p id="return_from_handleSession">
            <?php
            if(isset($errorMsg) && isset($_POST['first_login']))
            {
                echo '<input type="hidden" name="first_login" value="true">';
                echo '<br><input type="password" name="psk" value="'.$_POST['psk'].'" placeholder="'.PSK.'">';
                echo '<br><input type="password" name="new_password" placeholder="'.NEW_PASSWD.'">';
                echo '<br><a href="#" onclick="document.getElementById(\'the_form\').submit();">'.CONNECTION.'</a>';
                echo '<br><font color="red">'.$errorMsg.'</font>';

            }elseif(isset($errorMsg))
            {
                echo '<br><input type="password" name="password" placeholder="'.PASSWD.'">';
                echo '<br><br><a href="#" onclick="document.getElementById(\'the_form\').submit();">'.CONNECTION.'</a>';
                echo '<br><font color="red">'.$errorMsg.'</font>';
            }
            ?>
        </p>
    </form>
    </div>
</body>
</html>