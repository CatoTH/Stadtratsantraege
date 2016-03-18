<?php

namespace app\components;

use app\models\Email;
use yii\helpers\Html;
use Zend\Mail\Header\ContentType;

class Mailgun
{
    private $apiKey;

    /**
     * @param array $params
     *
     * @throws \Exception
     */
    public function __construct($params)
    {
        if (!isset($params['mandrillApiKey'])) {
            throw new \Exception('Mandrill\'s apiKey not set');
        }
        $this->apiKey = $params['mandrillApiKey'];
    }

    /**
     * @param string $subject
     * @param string $plain
     * @param string $html
     * @param string $messageId
     *
     * @return \Zend\Mail\Message
     */
    public function createMessage($subject, $plain, $html, $messageId)
    {
        $mail = $this->getMessageClass();
        $mail->setFrom(\Yii::$app->params['mailFromEmail'], \Yii::$app->params['mailFromName']);
        $mail->setSubject($subject);
        $mail->setEncoding('UTF-8');

        $mId = new \Zend\Mail\Header\MessageId();
        $mId->setId($messageId);
        $mail->getHeaders()->addHeader($mId);

        if ($html == '') {
            $mail->setBody($plain);
            $content = new \Zend\Mail\Header\ContentType();
            $content->setType('text/plain');
            $content->addParameter('charset', 'UTF-8');
            $mail->getHeaders()->addHeader($content);
        } else {
            $html = '<!DOCTYPE html><html>
            <head><meta charset="utf-8"><title>' . Html::encode($subject) . '</title>
            </head><body>' . $html . '</body></html>';

            $converter   = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
            $contentHtml = $converter->convert($html);
            $contentHtml = preg_replace("/ data\\-[a-z0-9_-]+=\"[^\"]*\"/siu", "", $contentHtml);

            $textPart          = new \Zend\Mime\Part($plain);
            $textPart->type    = 'text/plain';
            $textPart->charset = 'UTF-8';
            $htmlPart          = new \Zend\Mime\Part($contentHtml);
            $htmlPart->type    = 'text/html';
            $htmlPart->charset = 'UTF-8';
            $mimem             = new \Zend\Mime\Message();
            $mimem->setParts([$textPart, $htmlPart]);

            $mail->setBody($mimem);
            /** @var ContentType $contentType */
            $contentType = $mail->getHeaders()->get('content-type');
            $contentType->setType('multipart/alternative');
        }

        return $mail;
    }

    /**
     * @param \Zend\Mail\Message $message
     * @param string $toEmail
     *
     * @return string
     */
    public function send($message, $toEmail)
    {
        if (YII_ENV == 'test' || mb_strpos($toEmail, '@example.org') !== false) {
            return Email::STATUS_SKIPPED_OTHER;
        }

        $message->setTo($toEmail);
        $transport = $this->getTransport();
        $transport->send($message);

        return Email::STATUS_SENT;
    }

    /**
     * @return \Zend\Mail\Message
     */
    public function getMessageClass()
    {
        $message = new \SlmMail\Mail\Message\Mailgun();
        $message->setOption('tracking', false);

        return $message;
    }

    /**
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        $client = new \Zend\Http\Client();
        $client->setAdapter(new \Zend\Http\Client\Adapter\Curl());
        $service = new \SlmMail\Service\MailgunService('hoessl.eu', \Yii::$app->params['mailgunApiKey']);
        $service->setClient($client);

        return new \SlmMail\Mail\Transport\HttpTransport($service);
    }

    /**
     * @param string $toEmail
     * @param string $subject
     * @param string $textPlain
     * @param string $textHtml
     *
     * @throws \Exception
     */
    public static function sendWithLog($toEmail, $subject, $textPlain, $textHtml = '')
    {
        $mailer = new static(\Yii::$app->params);

        $messageId = explode('@', \Yii::$app->params['mailFromEmail']);
        $messageId = uniqid() . '@' . $messageId[1];

        $exception = null;
        try {
            $message = $mailer->createMessage(
                $subject,
                $textPlain,
                $textHtml,
                $messageId
            );
            $status  = $mailer->send($message, $toEmail);
        } catch (\Exception $e) {
            $status    = Email::STATUS_DELIVERY_ERROR;
            $exception = $e;
        }

        $obj            = new Email();
        $obj->toEmail   = $toEmail;
        $obj->subject   = $subject;
        $obj->text      = $textPlain;
        $obj->dateSent  = date('Y-m-d H:i:s');
        $obj->status    = $status;
        $obj->messageId = $messageId;
        $obj->save();

        if ($exception) {
            /** @var \Exception $exception */
            throw new \Exception($exception->getMessage());
        }

        if (YII_ENV == 'test') {
            \Yii::$app->session->setFlash('email', 'E-Mail sent to: ' . $toEmail);
        }
    }
}
