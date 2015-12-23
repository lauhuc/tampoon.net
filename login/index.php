<?php namespace login;

use Manager\DatabaseManager;
use Model\InitConsts;

session_start();
include_once '../translations/label_'.$_SESSION['locale'].'.php';

if(count($_POST) > 0)
{
    $b_for_check_empty = TRUE;

    foreach($_POST as $s_key => $s_value):

        $s_strip_spaces = trim($s_value);

        if(empty($s_strip_spaces))
        {
            $b_for_check_empty = FALSE;
            BREAK;

        }else $a_cleaned_values [$s_key] = $s_strip_spaces;

    endforeach;

    if($b_for_check_empty)
    {
        require_once '../Model/InitConsts.php';

        if(isset($_POST['first_login']))
        {
            if(sha1($a_cleaned_values['psk']) === InitConsts::HASH_PASSWD) //one always check the hash value
            {
                if(strlen($a_cleaned_values['new_password']) > 5)
                {
                    if(sha1($a_cleaned_values['new_password']) !== InitConsts::HASH_PASSWD)// the new password cannot be as the PSK
                    {
                        require_once '../Manager/DatabaseManager.php';

                        $mm = new DatabaseManager;

                        $output = $mm->updatePasswdAndlogin($a_cleaned_values);

                        if(is_bool($output))
                        {
                            $_SESSION['customer'] = $a_cleaned_values['email'];
                            header('Location: ../order');

                        }else $errorMsg = $output;

                    }else $errorMsg = DEFINE_NEW_PASSWD;

                }else $errorMsg = MIN_LEN_PASSWD;

            }else $errorMsg = CORRECT_PSK;

        }else $errorMsg = '@TODO!';

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
        <input type="email" name="email" placeholder="Email" onkeyup="handleSession(this.value);" value="<?php echo (!empty($a_cleaned_values['email'])) ? $a_cleaned_values['email']: '' ?>"><br>
        <p id="return_from_handleSession">
            <?php
            if(isset($errorMsg))
            {
                if(isset($_POST['first_login'])) echo '<input type="hidden" name="first_login" value="true">';

                echo '<br><input type="password" name="psk" value="'.$a_cleaned_values['psk'].'" placeholder="'.PSK.'">';
                echo '<br><input type="password" name="new_password" placeholder="'.NEW_PASSWD.'">';
                echo '<br><a href="#" onclick="document.getElementById(\'the_form\').submit();">'.CONNECTION.'</a>';
                echo '<br><font color="red">'.$errorMsg.'</font>';
            }
            ?>
        </p>
    </form>
    </div>
</body>
</html>