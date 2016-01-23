<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @package app\models
 *
 * @property int $id
 * @property int|null $ris_id
 * @property string $name
 * @property int $fraktionsmitglied
 *
 * @property Antrag[] $antraege
 * @property Antrag[] $initiierteAntraege
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInitiierteAntraege()
    {
        return $this->hasMany(Antrag::class, ['id' => 'antrag_id'])
                    ->viaTable('antraege_initiatorinnen', ['stadtraetin_id' => 'id']);
    }

    /** @var null|Stadtraetin[] */
    private static $fraktionsmitglieder = null;

    public static function alleFraktionsmitglieder()
    {
        if (static::$fraktionsmitglieder === null) {
            static::$fraktionsmitglieder = static::find()->where(['fraktionsmitglied' => 1])->orderBy('name')->all();
        }

        return static::$fraktionsmitglieder;
    }
}