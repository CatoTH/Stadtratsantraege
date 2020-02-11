<?php

namespace app\components;

use app\models\Email;

class Mailjet
{
    private $apiKey;
    private $secret;

    /**
     * @param array $params
     *
     * @throws \Exception
     */
    public function __construct($params)
    {
        if (!isset($params['mailjetApiKey'])) {
            throw new \Exception('Mailjet\'s mailjetApiKey not set');
        }
        $this->apiKey = $params['mailjetApiKey'];
        $this->secret = $params['mailjetApiSecret'];
    }

    public function createMessage(string $subject, string $plain, string $html, string $messageId)
    {
        return [
            'From'     => [
                'Email' => \Yii::$app->params['mailFromEmail'],
                'Name'  => \Yii::$app->params['mailFromName']
            ],
            'Subject'  => $subject,
            'TextPart' => $plain,
            'HTMLPart' => $html,
            'Headers'  => [
                'Precedence' => 'bulk',
            ]
        ];
    }

    /**
     * @param array $message
     * @param string $toEmail
     *
     * @return string
     */
    public function send($message, $toEmail)
    {
        if (YII_ENV == 'test' || mb_strpos($toEmail, '@example.org') !== false) {
            return Email::STATUS_SKIPPED_OTHER;
        }

        $message['To'] = [
            [
                'Email' => $toEmail,
                'Name'  => $toEmail,
            ]
        ];
        $mailjet       = new \Mailjet\Client($this->apiKey, $this->secret, true, ['version' => 'v3.1']);
        $response      = $mailjet->post(\Mailjet\Resources::$Email, ['body' => ['Messages' => [$message]]]);
        if ($response->success()) {
            return Email::STATUS_SENT;
        } else {
            var_dump($response->getBody());

            return Email::STATUS_DELIVERY_ERROR;
        }
    }

    /**
     * @param string $toEmail
     * @param string $subject
     * @param string $textPlain
     * @param string $textHtml
     *
     * @throws \Exception
     */
    public static function sendWithLog(string $toEmail, string $subject, string $textPlain, string $textHtml = '')
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
