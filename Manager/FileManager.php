<?php

/**
 * Created by PhpStorm.
 * User: art
 * Date: 15/12/15
 * Time: 15:42
 */
namespace Manager;

use Model\InitConsts as IC;

class FileManager implements IC
{
    /**
     * @var bool|string
     */
    public $date;

    /**
     * @var
     */
    public $csvPath;

    /**
     * @var
     */
    public $ref;

    /**
     * @var
     */
    public $pdfPath;

    /**
     * OrderManager constructor.
     */
    public function __construct($p1_email, $p2_date)
    {
        $this->date = $p2_date;
        $this->ref = $p1_email.'_'.$this->date;
    }

    /**
     * @param array $p1_datas_post
     * @return bool|string
     */
    public function formatAndWriteCSV(array $p1_datas_post)
    {
        $this->csvPath  = pathinfo(__DIR__)['dirname'].IC::DS.'csv'.IC::DS.$this->ref.'.csv';

        $csv = '';
        $i = 0;

        foreach($p1_datas_post as $k => $v):

            if(FALSE !== stripos($k, '_'))      //the post key that has underscore correspond to tampoon ref
            {
                $i++;
                $reference = strtr($k, '_', ' ');

                if($i === 1)
                {
                    $csv .= $reference.';'.trim($v).';'.$_POST['quantityTampoon'].';'.strtr($_POST['total'], '.', ',').' '.html_entity_decode(IC::CURRENCY[0]).PHP_EOL;

                }else $csv .= $reference.';'.trim($v).PHP_EOL;
            }

        endforeach;

        if(FALSE !== file_put_contents($this->csvPath, 'Item Reference;Quantity;Sum;Total'.PHP_EOL.$csv))
        {
            return TRUE;

        }else return CSV_NOT_SAVED;
    }

    /**
     * @param array $p1_datas_post
     * @return bool|string
     */
    public function formatAndWritePDF(array $p1_datas_post)
    {
        $this->pdfPath = pathinfo(__DIR__)['dirname'].IC::DS.'pdf'.IC::DS.$this->ref.'.pdf';

        $htmlOutput = '<div id="order_details"><h1>'.PURCHASE_ORDER.'</h1><br>Total Tampoon '.$p1_datas_post['quantityTampoon'];

        if($p1_datas_post['standingUnit'] === '1')
        {
            $htmlOutput .= '<br><font color="green">1 '.STANDING_UNIT.' 27 '.UNITS.'</font>';

        }elseif($p1_datas_post['standingUnit'] === '2')
        {
            $htmlOutput .= '<br><font color="green">1 '.STANDING_UNIT.' 45 '.UNITS.'</font>';
        }

        $htmlOutput .= '<br>Total h.t., '.CARRIAGE_FREE.': '.$p1_datas_post['total'].' '.IC::CURRENCY[0];
        $htmlOutput .= '<br>Date: <b>'.$this->date.'</b>';
        $htmlOutput .= '<br>'.strtoupper(CLIENT).': <b>'.$p1_datas_post['clientEmail'].'</b></div>';
        $htmlOutput .= '<div id="icons">'.PHP_EOL.'<table>';

        $i = 0;

        foreach($p1_datas_post as $k => $v):

            if(FALSE !== stripos($k, '_'))      //the post key that has underscore correspond to tampoon ref
            {
                $reference = strtr($k, '_', ' ');

                $i++;

                if($i === 6)
                {
                    $i = 0;

                    $htmlOutput .= '<td><img src="../icon/'.$reference.'.jpg" style="width: 25px;"></td><td>'.$reference.'</td><td>'.$v.'</td></tr>'.PHP_EOL;


                }elseif($i === 1)
                {
                    $htmlOutput .= '<tr><td><img src="../icon/'.$reference.'.jpg" style="width: 25px;"></td><td>'.$reference.'</td><td>'.$v.'</td>'.PHP_EOL;

                }else $htmlOutput .= '<td><img src="../icon/'.$reference.'.jpg" style="width: 25px;"></td><td>'.$reference.'</td><td>'.$v.'</td>'.PHP_EOL;

            }

        endforeach;

        if(FALSE !== stripos(substr($htmlOutput, strlen($htmlOutput) -5), 'tr'))
        {
            $htmlOutput .= '</table></div></div></body></html>';

        }else $htmlOutput .= '</tr></table></div></div></body></html>';

        $tmp = microtime(TRUE);

        file_put_contents('../htmTemplates/'.$tmp.'.htm', file_get_contents('../header.html').$htmlOutput);

        require '../vendor/autoload.php';

        // disable DOMPDF's internal autoloader if you are using Composer
        define('DOMPDF_ENABLE_AUTOLOAD', false);

        require_once '../vendor/dompdf/dompdf/dompdf_config.inc.php';

        $dompdf = new \DOMPDF;
        $dompdf->load_html(file_get_contents('../htmTemplates/'.$tmp.'.htm'));
        $dompdf->render();

        @unlink('../htmTemplates/'.$tmp.'.htm');

        if(FALSE !== file_put_contents($this->pdfPath, $dompdf->output()))
        {
            return TRUE;

        }else return PDF_NOT_SAVED;
    }
}