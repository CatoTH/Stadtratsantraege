<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @package app\models
 *
 * @property int $id
 * @property string $name
 *
 * @property Antrag[] $antraege
 */
class Tag extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'tags';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrag()
    {
        return $this->hasMany(Antrag::class, ['id' => 'antrag_id'])
            ->viaTable('antraege_tags', ['tag_id' => 'id']);
    }
}