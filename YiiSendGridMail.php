<?php
/**
 * @author Bryan Jayson Tan <admin@bryantan.info>
 * @link http://bryantan.info
 * @date 8/20/13
 * @time 4:09 PM
 *
 * @method array getTos()
 * @method YiiSendGridMail setTos(array $email_list)
 * @method YiiSendGridMail setTo($email)
 * @method YiiSendGridMail addTo($email, $name=null)
 * @method YiiSendGridMail removeTo($search_term)
 * @method string getFrom($as_array = false)
 * @method YiiSendGridMail setFrom($email)
 * @method string getFromName()
 * @method YiiSendGridMail setFromName($name)
 * @method string getReplyTo()
 * @method YiiSendGridMail setReplyTo($email)
 * @method array getCcs()
 * @method YiiSendGridMail setCcs(array $email_list)
 * @method YiiSendGridMail setCc($email)
 * @method YiiSendGridMail addCc($email)
 * @method YiiSendGridMail removeCc($email)
 * @method array getBccs()
 * @method YiiSendGridMail setBccs($email_list)
 * @method YiiSendGridMail setBcc($email)
 * @method YiiSendGridMail addBcc($email)
 * @method YiiSendGridMail removeBcc($email)
 * @method string getSubject()
 * @method YiiSendGridMail setSubject($subject)
 * @method string getText()
 * @method YiiSendGridMail setText($text)
 * @method string getHtml()
 * @method YiiSendGridMail setHtml($html)
 * @method array getAttachments()
 * @method YiiSendGridMail setAttachments(array $files)
 * @method YiiSendGridMail setAttachment($file)
 * @method YiiSendGridMail addAttachment($file)
 * @method YiiSendGridMail removeAttachment($file)
 * @method YiiSendGridMail setCategories($category_list)
 * @method YiiSendGridMail setCategory($category)
 * @method YiiSendGridMail addCategory($category)
 * @method YiiSendGridMail removeCategory($category)
 * @method YiiSendGridMail setSubstitutions($key_value_pairs)
 * @method YiiSendGridMail addSubstitution($from_value, array $to_values)
 * @method YiiSendGridMail setSections(array $key_value_pairs)
 * @method YiiSendGridMail addSection($from_value, $to_value)
 * @method YiiSendGridMail setUniqueArguments(array $key_value_pairs)
 * @method YiiSendGridMail addUniqueArgument($key, $value)
 * @method YiiSendGridMail setFilterSettings($filter_settings)
 * @method YiiSendGridMail addFilterSetting($filter_name, $parameter_name, $parameter_value)
 * @method array getHeaders()
 * @method string getHeadersJson()
 * @method YiiSendGridMail setHeaders($key_value_pairs)
 * @method YiiSendGridMail addHeader($key, $value)
 * @method YiiSendGridMail removeHeader($key)
 * @method bool useHeaders()
 * @method YiiSendGridMail setRecipientsInHeader($preference)
 */
class YiiSendGridMail extends CComponent
{
    public $componentName = 'sendGrid';

    private $_mail = null;

    public function getMail()
    {
        if ($this->_mail===null) {
            $this->_mail = new SendGrid\Mail();
        }

        return $this->_mail;
    }

    public function setView($viewFile,$data = array())
    {
        // if Yii::app()->controller doesn't exist create a dummy
        // controller to render the view (needed in the console app)
        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('YiiMail');

        // renderPartial won't work with CConsoleApplication, so use
        // renderInternal - this requires that we use an actual path to the
        // view rather than the usual alias

        $viewPath = $controller->getLayoutFile($viewFile);

        $body = $controller->renderFile($viewPath, array_merge($data, array('mail'=>$this)), true);

        return $this->getMail()->setHtml($body);
    }

    /**
     * @param string $name the method name
     * @param array $parameters method parameters
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        if(method_exists($this->getMail(), $name)) {
            return call_user_func_array(array($this->getMail(),$name),$parameters);
        }else {
            parent::__call($name, $parameters);
        }

    }
}
