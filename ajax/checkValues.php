<?php header('Content-type: text/html; charset="UTF-8";');

if(count($_POST) > 0)
{
    $i_sum = 0;

    $html_output = '<div style="width: 26px; height: 26px; border-radius: 13px; background-color: white; cursor: pointer;" onclick="document.getElementById(\'checkvalues\').style.visibility = \'hidden\';"></div>';
    $html_output .= '<h1>Confirmation</h1>';

    foreach($_POST as $k => $v):

        $s_value = trim($v);

        if(!empty($s_value))
        {
            $i_value = (int) $s_value;

            $html_output .= '<br>'.$k.' => '.$s_value.' (quantities)';

            $i_sum += $i_value;
        }

        endforeach;

    $html_output .= '<h2>Sum = '.$i_sum.'</h2>';

    echo $html_output.'<br><a href="#" onclick="processOrder('.$i_sum.');">Send</a>';

}else echo 'this page requires variables';