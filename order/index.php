<?php
namespace Order;
use Model\InitConsts as IC;
session_start();
require_once '../Model/InitConsts.php';

if(in_array(trim($_GET['lg']), IC::LOCALE))
{
    $_SESSION['locale'] = trim($_GET['lg']);

}else $_SESSION['locale'] = 'fr';

include_once '../translations/label_'.$_SESSION['locale'].'.php';

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Tampoon</title>
    <link rel="icon" href="../img/favicon.ico" />
    <meta name="description" content="Tampoon" />
    <script>
<?php
    //pass PHP consts to JavaScript
    echo 'var tampoonFirstRate = '.IC::TAMPOON_FIRST_RATE.';'.PHP_EOL;
    echo 'var tampoonSecondRate = '.IC::TAMPOON_SECOND_RATE.';'.PHP_EOL;
    echo 'var minimumQuantityOrder = '.IC::MINIMUM_Q_ORDER.';'.PHP_EOL;
    echo 'var currency = "'.IC::CURRENCY[0].'";'.PHP_EOL;
    echo 'var locale = "'.$_SESSION['locale'].'";'.PHP_EOL;
?>
    </script>
    <script type="text/javascript" src="../js/translations.js"></script>
    <script type="text/javascript" src="../js/script.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
</head>
<body>
<div id="checkvalues">
    <div onclick="document.getElementById('checkvalues').style.visibility = 'hidden';" id="btnClose"><div style="padding-top: 4px;"><b>X</b></div></div>
    <h1>Confirmation</h1>
    <hr/>
    <p id="return_from_checkvalues"></p>
</div>
<div id="top">
 <div>
     <img src="../img/logo-tp.png" style="border: none; width: 200px; margin-right: 10px;" />
 </div>
    <div>
        <form style="float: left">
        <select id="fillAction" onchange="if(this.value === 'all'){ fillAllWith1Q(); }else if(this.value == '1'){ fill50ValWithXQ(parseInt(this.value)); }else{ fillXQuantitiesWithXItems(1, 100); }">
            <option value="all"><?php echo FILL_ALL_WITH_ONE ?></option>
            <option value="1"><?php echo FILL_50_WITH_1 ?></option>
            <option value="2"><?php echo FILL_100_WITH_1 ?></option>
        </select>
            <br>
            <a href="#" onclick="clearAllInputsValues();"><?php echo CLEAR_VALUES ?></a>
        </form>
    </div>
    <div>
        <input type="text" id="num_items" placeholder="<?php echo DIFF_ITEMS ?>" style="width: 100px;">&nbsp;<input type="text" id="quantity" placeholder="<?php echo Q ?>" style="width: 50px;">
        <br>
        <a href="#" onclick="fillXQuantitiesWithXItems(document.getElementById('quantity').value, document.getElementById('num_items').value);"><?php echo FILL ?></a>
     </div>
    <div>
        <h3><?php echo STANDING_UNIT ?></h3>
        <label><?php echo NO ?></label><input type="radio" value="3" name="standing_unit" checked>
        <label>27 <?php echo UNITS ?></label><input type="radio" value="1" name="standing_unit" >
        <label>45 <?php echo UNITS ?></label><input type="radio" value="2" name="standing_unit" >
    </div>
    <div id="infos"><p id="return_from_makeSum" style="margin-bottom: 0;"></p></div>

</div>
<div id="main">
    <form method="post" name="the_form">
<?php
require_once '../Manager/DatabaseManager.php';

$dbm = new \Manager\DatabaseManager;

$outputDBM = $dbm->fetchTampoonInfos(FALSE);

    foreach($outputDBM as $rows):

        if($rows['quantity'] > 0)
        {
            $icon = '../icon'.IC::DS.$rows['reference'].'.jpg';

            echo '<div class="container_icon" id="container_'.$rows['reference'].'"><table><tr><td><img class="icon" src="'.$icon.'" /></td></tr>';
            echo '<tr><td>'.$rows['reference'].'</td></tr>';
            echo '<tr><td>';
            echo '<input placeholder="'.$rows['quantity'].'" type="number" min="0" max="'.$rows['quantity'].'" id="'.$rows['reference'].'" name="'.$rows['reference'].'" onclick="makeSum();" ';
            echo 'onchange="switchDivDisplay(this.value, \'container_'.$rows['reference'].'\')" ';
            echo 'onfocus="if(document.getElementById(\'checkvalues\').style.visibility === \'visible\') document.getElementById(\'checkvalues\').style.visibility = \'hidden\';"/>';
            echo '&nbsp;dispo</td></tr>';
            echo '</table></div>';

        }else $unavailableItems[] = $rows;

    endforeach;
?></form>
    </div>
<div id="bottom">
    <?php

    if(isset($unavailableItems))
    {
        echo '<hr/><h1>'.INDEX_BOTTOM_MSG.'</h1>';

        foreach ($unavailableItems as $rows):

            $icon = '../icon'.IC::DS.$rows['reference'].'.jpg';

            echo '<div class="container_icon" id="container_'.$rows['reference'].'"><table><tr><td><img class="icon" src="'.$icon.'" /></td></tr>';
            echo '<tr><td>'.$rows['reference'].'</td></tr>';
            echo '</table></div>';

        endforeach;
    }

    ?>
    </div>
</body>
</html>