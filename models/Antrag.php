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
 * @property string $typ
 * @property string $gestellt_am
 * @property string $bearbeitungsfrist
 * @property string|null $bearbeitungsfrist_benachrichtigung
 * @property string|null $fristverlaengerung
 * @property string|null $fristverlaengerung_benachrichtigung
 * @property string $erledigt_am
 * @property string $status
 * @property string $status_override
 * @property string $notiz
 *
 * @property Tag[] $tags
 * @property Stadtraetin[] $stadtraetinnen
 * @property Stadtraetin[] $initiatorinnen
 * @property Dokument[] $dokumente
 */
class Antrag extends ActiveRecord
{
    const SORT_DATUM_FRIST = 0;
    const SORT_TITEL = 1;
    const SORT_STATUS = 2;
    const SORT_DATUM_ANTRAG = 3;

    public static $STATI = [
        0 => 'erledigt',
        1 => 'In Bearbeitung',
        2 => 'registriert',
        3 => 'zugeleitet',
        4 => 'aufgegriffen',
        5 => 'Sitzungsvorlage',
    ];

    public static $TYPEN = [
        0 => 'Antrag',
        1 => 'Anfrage',
        2 => 'Ergaenzungsantrag',
        3 => 'Aenderungsantrag',
    ];

    public static $THEMEN = [
        'Abfallwirtschaft',
        'Arbeit und Wirtschaft',
        'Artenschutz',
        'Bau',
        'Berufliche Bildung',
        'Beschaffung / Fairtrade',
        'Bildung Allgemein',
        'Bürgerschaftliches Engagement',
        'Datenschutz',
        'Demokratie / Bürgerbeteiligung',
        'Energie',
        'Ernährung',
        'Europa / Internationales',
        'Finanzen',
        'Flucht / Asyl',
        'Fußverkehr',
        'Gender / Frauen',
        'Gesundheit',
        'IT/Digitales',
        'Inklusion',
        'KiTa',
        'Kinder / Jugend',
        'Klimaschutz',
        'Kommunale Beschäftigung',
        'Kommunales',
        'Kultur',
        'Luft',
        'Lärm',
        'MIV',
        'Migration',
        'Naturschutz',
        'Öffentliche Sicherheit',
        'Öffentlicher Raum',
        'ÖPNV',
        'Planung / Stadtentwicklung',
        'Radverkehr',
        'Rechtsextremismus',
        'Schule',
        'Senior*innen',
        'Sexuelle Identität',
        'Soziales',
        'Sport',
        'Verkehr Allgemein',
        'Verwaltung/Personal',
        'Wohnen',
        'Wohnungslosigkeit',
    ];

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
    public function getInitiatorinnen()
    {
        return $this->hasMany(Stadtraetin::class, ['id' => 'stadtraetin_id'])
                    ->viaTable('antraege_initiatorinnen', ['antrag_id' => 'id']);
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
     * @return \yii\db\ActiveQuery
     */
    public function getDokumente()
    {
        return $this->hasMany(Dokument::class, ['antrag_id' => 'id']);
    }

    /**
     * @return int
     */
    public function getStatusId()
    {
        foreach (static::$STATI as $id => $name) {
            if (mb_strtolower($this->status) == mb_strtolower($name)) {
                return $id;
            }
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getTypId()
    {
        foreach (static::$TYPEN as $id => $name) {
            if (mb_strtolower($this->typ) == mb_strtolower($name)) {
                return $id;
            }
        }

        return 0;
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

    /**
     * @return bool
     */
    public function istAbgelaufen()
    {
        if (str_replace('-', '', $this->bearbeitungsfrist) > date('Ymd')) {
            return false;
        }
        if ($this->fristverlaengerung != '' && str_replace('-', '', $this->fristverlaengerung) > date('Ymd')) {
            return false;
        }

        return !$this->istErledigt();
    }

    /**
     * @return bool
     */
    public function istErledigt()
    {
        if ($this->status_override == '') {
            return in_array(strtolower($this->status), ['erledigt', 'zurückgezogen']);
        } else {
            return (strtolower($this->status_override) == 'erledigt');
        }
    }

    /**
     * @return Antrag[]
     */
    public static function getAbgelaufene()
    {
        $sql = 'bearbeitungsfrist <= CURRENT_DATE() ' .
               'AND (fristverlaengerung IS NULL OR fristverlaengerung <= CURRENT_DATE()) ' .
               'AND NOT ((status_override = "" AND status = "erledigt") OR status_override = "erledigt") ' .
               'AND bearbeitungsfrist_benachrichtigung IS NULL';

        return Antrag::find()->where($sql)->all();
    }
}
