<?php
namespace backend\modules\booking\models;

use yii\base\Model;

class FindRoomForm extends Model
{
    public $date;
    public $startTime;
    public $endTime;
    public $minCapacity;

    public function rules()
    {
        return [
            [['date', 'startTime', 'endTime'], 'required'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['startTime', 'endTime'], 'time', 'format' => 'php:H:i'],
            [['minCapacity'], 'integer', 'min' => 1],
            ['endTime', 'compare', 'compareAttribute' => 'startTime', 'operator' => '>', 'message' => 'End time must be later than start time.'],
        ];
    }
}