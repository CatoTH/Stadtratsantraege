<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @package app\models
 *
 * @property int $dokument_id
 * @property int $antrag_id
 * @property string $url
 * @property string $datum
 * @property string $titel
 *
 * @property Antrag $antrag
 */
class Dokument extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'dokumente';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrag()
    {
        return $this->hasOne(Antrag::class, ['id' => 'antrag_id']);
    }
}
