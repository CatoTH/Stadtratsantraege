<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @package app\models
 *
 * @property int $id
 * @property int|null $ris_id
 * @property string $name
 *
 * @property Antrag[] $antraege
 */
class Stadtraetin extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'stadtraetinnen';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntraege()
    {
        return $this->hasMany(Antrag::class, ['id' => 'antrag_id'])
            ->viaTable('antraege_stadtraetinnen', ['stadtraetin_id' => 'id']);
    }
}