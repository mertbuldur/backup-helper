<?php
namespace  MTBackup;
require_once 'Mail/PHPMailer.php';
require_once 'Mail/SMTP.php';
require_once 'Mail/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class BackupService
{
    private $db;
    private $mailOptions;
    private $string;
    private $filePath;
    public function __construct($database = [],$mail = []){
        $this->mailOptions = $mail;
        $this->filePath = "backup/backup-".date("Y-m-d").".sql";
        if(isset($database['host']) && isset($database['database']) && isset($database['user']) && isset($database['password'])) {
            try {
                $this->db = new \PDO("mysql:host=" . $database['host'] . ";
            dbname=" . $database['database'] . ";charset=utf8", $database['user'], $database['password']);
            } catch (\Exception $e) {
                var_dump($e);

            }
        }
        else
        {
            echo "Bilgileri Gönderiniz";
        }
    }

    public function backup(): BackupService
    {

        $resultQuery = $this->db->query("SHOW TABLES");
        $return = '';
        while($table = $resultQuery->fetch()){

            $result = $this->db->query('SELECT * FROM '.$table[0]);
            $num_fields = $result->columnCount();
            $return.= 'DROP TABLE  IF EXISTS '.$table[0].';';
            $row = $this->db->query('SHOW CREATE TABLE '.$table[0])->fetch();
            $return.="\n\n".$row[1].";\n\n";
            for($i = 0; $i<$num_fields;$i++){
                while ($rowC = $result->fetch()){
                    $return.= 'INSERT INTO '.$table[0].' VALUES(';
                    for($j = 0; $j < $num_fields;$j++){
                        $rowC[$j] = addslashes($rowC[$j]);
                        $rowC[$j] = str_replace("\n","\\n",$rowC[$j]);
                        $value = ($rowC[$j] == '' || $rowC[$j] == NULL ) ? 'NULL' : '"'.$rowC[$j].'"';
                        if(isset($rowC[$j])){ $return.=$value; } else { $return.= NULL;}
                        if($j < ($num_fields-1)){ $return.= ',';}
                    }
                    $return.= ");\n";
                }
                $return.= "\n\n\n";
            }
        }
        $this->string = $return;
        return $this;
    }

    public function write(): BackupService
    {

        if(file_exists($this->filePath)){ unlink($this->filePath);}
        $handle = fopen($this->filePath,"w+");
        $write = fwrite($handle,$this->string);
        fclose($handle);
        return $this;
    }

    public function mail(){
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->mailOptions['smtp'];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $this->mailOptions['username'];                     //SMTP username
            $mail->Password   = $this->mailOptions['password'];                               //SMTP password
            $mail->SMTPSecure = $this->mailOptions['secure'];            //Enable implicit TLS encryption
            $mail->Port       = $this->mailOptions['port'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($this->mailOptions['username'], 'MtBackUP');
            $mail->addAddress($this->mailOptions['backup_mail']);               //Name is optional

            //Attachments
            $mail->addAttachment($this->filePath);    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Yedekleme - '.date("Y-m-d");
            $mail->Body    = 'Bu mesaj otomatik olarak gönderilmiştir veritabanı yedeğiniz ektedir.';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
