INSTALLATION
------------

```php
'imports' => array(
    'ext.sendgrid.YiiSendGridMail'
),
'components' => array(
    .....
    'sendGrid' => array(
        'class' => 'ext.sendgrid.YiiSendGrid',
        'username' => 'my_username',
        'password' => 'my_password',
        'api' => 'smtp' // can be smtp or web
        // custom behavior
        'logging' => true, // default to true
        'dryRun' => false,
    ),
),
```

##Usage

**send email using text**

```php

/* @var $sendGrid YiiSendGridMail */
$sendGrid = Yii::app()->sendGrid;

$mail = new YiiSendGridMail();
$mail->addTo('bryglen16@yahoo.com','Bryan Tan')
    ->setFrom('bryantan16@gmail.com')
    ->setSubject('Test Send Grid')
    ->setText('Hello World');

$response = $sendGrid->send($mail);
```

**send email using HTML**

```php

/* @var $sendGrid YiiSendGridMail */
$sendGrid = Yii::app()->sendGrid;

$mail = new YiiSendGridMail();
$mail->addTo('admin@webmaster.com','Bryan Tan')
    ->setFrom('bryantan16@gmail.com')
    ->setSubject('Test Send Grid')
    ->setHtml('<strong>Hello World</strong>');

$response = $sendGrid->send($mail);
```

**send email using Yii view**

```php

/* @var $sendGrid YiiSendGridMail */
$mail = new YiiSendGridMail();
$mail->setView('/mail/account-block',array('model' => new User()));
$mail->addTo('bryglen16@yahoo.com','Bryan Tan')
    ->setFrom('bryantan16@gmail.com')
    ->setSubject('Test Send Grid');

$response = $sendGrid->send($mail);
```