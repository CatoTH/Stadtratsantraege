<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @package app\models
 *
 * @property int $id
 * @property string $toEmail
 * @property string $dateSent
 * @property string $subject
 * @property string $text
 * @property string $messageId
 * @property int $status
 * @property string $error
 */
class EMail extends ActiveRecord
{
    const STATUS_SENT              = 0;
    const STATUS_SKIPPED_BLACKLIST = 1;
    const STATUS_DELIVERY_ERROR    = 2;
    const STATUS_SKIPPED_OTHER     = 3;

    /**
     * @return string[]
     */
    public static function getStatusNames()
    {
        return [
            static::STATUS_SENT              => 'Verschickt',
            static::STATUS_SKIPPED_BLACKLIST => 'Nicht verschickt (E-Mail-Blacklist)',
            static::STATUS_DELIVERY_ERROR    => 'Versandfehler',
            static::STATUS_SKIPPED_OTHER     => 'Übersprungen',
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'email';
    }
}
