<?php

/**
 * @author Bryan Jayson Tan <admin@bryantan.info>
 * @link http://bryantan.info
 * @date 8/20/13
 * @time 4:09 PM
 */
class YiiSendGrid extends CApplicationComponent
{
    const API_SMTP = 'smtp';
    const API_WEB = 'web';

    public $username;
    public $password;
    public $api;

    public $success = false;

    /**
     * @var bool whether to log messages using Yii::log().
     * Defaults to true.
     */
    public $logging = true;

    /**
     * @var bool whether to disable actually sending mail.
     * Defaults to false.
     */
    public $dryRun = false;

    private $_client;

    public function init()
    {
        if (!$this->username) {
            throw new CException('SendGrid Username cannot be empty');
        }
        if (!$this->password) {
            throw new CException('SendGrid Password cannot be empty');
        }

        if (!in_array($this->api,array(self::API_SMTP,self::API_WEB))) {
            throw new CException(sprintf("SendGrid API must be %s or %s",self::API_SMTP,self::API_WEB));
        }

        $this->initAutoloader();

        parent::init();
    }

    /**
     * @return SendGrid
     */
    public function getClient()
    {
        if ($this->_client===null) {
            $sendgrid = new SendGrid($this->username,$this->password);

            $this->_client = $sendgrid;
        }
        return $this->_client;
    }

    /**
     * example of valid email send
     * <pre>
     * {"message":"success"}
     * </pre>
     * @param YiiSendGridMail $mail
     * @return string the json response
     */
    public function send(YiiSendGridMail $mail)
    {
        if ($this->dryRun===true) {
            if ($this->logging===true) {
                $this->log($mail);
            }
            return true;
        }
        // send an email
        $api = $this->api;
        $response = $this->getClient()->$api->send($mail->getMail());

        $checkResponse = json_decode($response);
        $this->success = (isset($checkResponse->message) && $checkResponse->message == 'success') ? true : false;

        if ($this->logging===true) {
            $this->log($mail,$this->success);
        }
        return $response;


    }

    public function log(YiiSendGridMail $mail,$success = true)
    {
        if ($mail->getHtml()) {
            $body = $mail->getHtml();
        }else {
            $body = $mail->getText();
        }

        $successMsg = $success === true ? "success" : "failed";
        $msg = 'Sending email '.$successMsg.' to '.implode(', ', $mail->getTos())."\n".
            $body
        ;
        Yii::log($msg, CLogger::LEVEL_INFO, 'ext.sendgrid.YiiSendGrid');
        return $msg;
    }

    public function initAutoLoader()
    {
        Yii::registerAutoloader(array(__CLASS__, 'autoload'));
    }

    /**
     * A modified version of SendGrid_loader.php tailored to work with Yii. There's generally no
     * need to register or use this directly.
     *
     * @param string $className
     */
    public static function autoload($className)
    {
        if(preg_match("/SendGrid/", $className) && $className != 'YiiSendGridMail')
        {
            $file = str_replace('\\', '/', "$className.php");
            require_once dirname(__FILE__).'/lib/' . $file;
        }
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function __call($method, $params)
    {
        $client = $this->getClient();
        if (method_exists($client, $method))
            return call_user_func_array(array($client, $method), $params);

        return parent::__call($method, $params);
    }
}
