<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @package app\models
 *
 * @property int $id
 * @property int|null $ris_id
 * @property string $antrags_nr
 * @property string $titel
 * @property string $gestellt_am
 * @property string $bearbeitungsfrist
 * @property string|null $fristverlaengerung
 * @property string $status
 * @property string $notiz
 * @property string|null $benachrichtigung
 *
 * @property Tag[] $tags
 * @property Stadtraetin[] $stadtraetinnen
 */
class Antrag extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'antraege';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStadtraetinnen()
    {
        return $this->hasMany(Stadtraetin::class, ['id' => 'stadtraetin_id'])
                    ->viaTable('antraege_stadtraetinnen', ['antrag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
                    ->viaTable('antraege_tags', ['antrag_id' => 'id']);
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public static function formatDate($date)
    {
        $part = explode("-", $date);
        if (count($part) != 3) {
            return "---";
        }

        return $part[2] . '.' . $part[1] . '.' . $part[0];
    }
}