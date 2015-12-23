<?php

namespace Manager;

use Model\InitConsts as IC;

class DatabaseManager implements IC
{
    /**
     * @var
     */
    protected $sqli;

    /**
     * @var
     */
    public $dateOrder;

    /**
     * DatabaseManager constructor.
     */
    public function __construct()
    {
        $this->sqli = new \mysqli(IC::MYSQLI_HOST, IC::MYSQLI_USER, IC::MYSQLI_PASSWORD, IC::MYSQLI_DBNAME);
        $this->dateOrder = date('Y-m-d h:i:s');
    }

    /**
     * @param $p1_email
     * @param null $p2_password
     * @return bool|string
     */
    public function fetchUser($p1_email, $p2_password = NULL)
    {
        if(NULL !== $p2_password)
        {
            $encryptedPassword = sha1($p2_password);

            $query = 'SELECT email, password
                      FROM tbl_customers
                      WHERE TRIM(email) = "'.$this->sqli->real_escape_string($p1_email).'"
                      AND password = "'.sha1($this->sqli->real_escape_string(trim($p2_password))).'"';

            $resultFetchMail = $this->sqli->query($query);

            if(is_object($resultFetchMail))
            {
                if ($resultFetchMail->num_rows === 1)
                {
                    $row = $resultFetchMail->fetch_array();

                    if($encryptedPassword !== IC::HASH_PASSWD && $row['password'] !== IC::HASH_PASSWD)
                    {
                        return TRUE;

                    }elseif($encryptedPassword === IC::HASH_PASSWD && $row['password'] === IC::HASH_PASSWD) //same initial hash for everyone BUT need to check also if the user typed the same password
                    {
                        return '<a href="../admin/changePassword.php?email='.$p1_email.'" target="_blank">'.UPDATE_PASSWORD.'</a>';

                    }elseif($encryptedPassword !== IC::HASH_PASSWD && $row['password'] === IC::HASH_PASSWD)
                    {
                        return CORRECT_PSK;
                    }

                }else return WRONG_CREDENTIALS;

            }else return $this->sqli->error;

        }else{

            $query = 'SELECT email, password FROM tbl_customers
                      WHERE TRIM(email) = "'.$this->sqli->real_escape_string($p1_email).'"';

            $resultFetchMail = $this->sqli->query($query);

            if(is_object($resultFetchMail))
            {
                if ($resultFetchMail->num_rows === 1)
                {
                    $row = $resultFetchMail->fetch_array();

                    if($row['password'] === IC::HASH_PASSWD) //same initial hash for everyone at the beginning
                    {
                        return FALSE;

                    }elseif($row['password'] !== IC::HASH_PASSWD)
                    {
                        return TRUE;        // most frequent moment where user exists with other passwd than PSK
                    }

                }elseif($resultFetchMail->num_rows > 1)
                {
                    return DUPLICATED_MAILS_IN_DB;

                }elseif($resultFetchMail->num_rows === 0)
                {
                    return NO_INSCRIPTIONS_ALLOWED;
                }

            }else return $this->sqli->error;
        }
    }

    /**
     * @param array $a
     * @return bool|string
     */
    public function updateUserPassword(array $a)
    {
        if(strlen($a['password_1']) > 5)
        {
            if($a['password_1'] === $a['password_2'])
            {

                if(sha1($a['password_1']) !== IC::HASH_PASSWD && sha1($a['password_2']) !== IC::HASH_PASSWD)
                {
                    $query = 'UPDATE tbl_customers
                              SET password = "'.sha1($this->sqli->real_escape_string($a['password_1'])).'"
                              WHERE TRIM(email) = "'.$this->sqli->real_escape_string($a['email']).'"';

                    $result = $this->sqli->query($query);

                    if($result)
                    {
                        if($this->sqli->affected_rows === 1)
                        {
                            return TRUE;

                        }else return UNEXISTING_EMAIL;

                    }else return $this->sqli->error;

                }else return DEFINE_NEW_PASSWD;

            }else return MATCH_PASSWORDS;

        }else return MIN_LEN_PASSWD;
    }

    /**
     * @param bool $p1_display_only_available
     * @return \Generator
     */
    public function fetchTampoonInfos($p1_display_only_available = TRUE)
    {
        $result = $this->sqli->query('SELECT * FROM tbl_tampoons '.(($p1_display_only_available) ? 'WHERE quantity > 0' : ''));

        if(is_object($result))
        {
            if ($result->num_rows > 0)
            {
                while ($rows = $result->fetch_array()) yield $rows;

            }else yield NO_ITEM_FOUND_IN_DB;

        }else yield $this->sqli->error;
    }

    /**
     * @param array $p1_datas_post
     * @param $p2_filesSaved
     * @return bool|string
     */
    public function saveOrder(array $p1_datas_post, $p2_filesSaved)
    {
        $queryOne = 'INSERT INTO tbl_orders
                    SET id_customer = (SELECT id FROM tbl_customers WHERE TRIM(tbl_customers.email) = "'.$this->sqli->real_escape_string($p1_datas_post['clientEmail']).'"),
                    date_order = "'.$this->dateOrder.'",
                    total = "'.$this->sqli->real_escape_string($p1_datas_post['total']).'",
                    status = '.$p2_filesSaved.',
                    id_standing_units ='.(int)$this->sqli->real_escape_string($p1_datas_post['standingUnit']);

        $resultOne = $this->sqli->query($queryOne);

        if($resultOne)
        {
            $insertIdQueryOne = $this->sqli->insert_id;

            $onlyTampoonInfos = [];

            foreach($p1_datas_post as $k => $v):

                if(FALSE !== stripos($k, '_'))      //the post key that has underscore correspond to tampoon ref
                {
                    $tampoonRef = strtr($k, '_', ' ');
                    $onlyTampoonInfos[$tampoonRef] = $v;

                    $queryTwo = 'INSERT INTO tbl_orders_details
                                  SET id_order = '.$insertIdQueryOne.',
                                    id_tampoon = (SELECT id FROM tbl_tampoons WHERE tbl_tampoons.reference = "'.$tampoonRef.'"),
                                    quantity = '.(int)$v;

                    $resultTwo = $this->sqli->query($queryTwo);

                    if(!$resultTwo) return $this->sqli->error;
                }

            endforeach;

            return $this->updateTampoonQuantities($onlyTampoonInfos);

        }else return $this->sqli->error;

    }
    
    /**
     * @param array $p1_tampoon_infos
     * @return bool|string
     */
    public function updateTampoonQuantities(array $p1_tampoon_infos)
    {
        foreach($p1_tampoon_infos as $k => $v):

            $query = 'UPDATE tbl_tampoons AS tp1 INNER JOIN tbl_tampoons AS tp2 ON tp1.reference = tp2.reference AND tp1.reference ="'.$k.'" SET tp1.quantity = (tp2.quantity - '.(int)$v.')';

            $result = $this->sqli->query($query);

            if($result){

                mysqli_free_result($result); //don't remove this line

            }else return $this->sqli->error;

        endforeach;

        return TRUE;
    }

    public function updatePasswdAndlogin(array $datasPost)
    {

        $query = 'UPDATE tbl_customers
                  SET password = "'.sha1($this->sqli->real_escape_string($datasPost['new_password'])).'"
                  WHERE TRIM(email) = "'.$this->sqli->real_escape_string($datasPost['email']).'"
                  AND password = "'.IC::HASH_PASSWD.'"';

        $result = $this->sqli->query($query);

        if($result)
        {
            $affectedRows = $this->sqli->affected_rows;

            if ($affectedRows === 1)
            {
                return TRUE;

            }elseif($affectedRows === 0)
            {
                return UNEXISTING_EMAIL;

            }elseif($affectedRows > 1)
            {
                return DUPLICATED_MAILS_IN_DB;
            }

        }else return $this->sqli->error;
    }
}