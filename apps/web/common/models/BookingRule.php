<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Admin-configurable booking policy rules.
 *
 * @property int    $id
 * @property string $rule_key
 * @property string $rule_value
 * @property string $description
 * @property string $updated_at
 */
class BookingRule extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%booking_rules}}';
    }

    public function rules()
    {
        return [
            [['rule_key', 'rule_value'], 'required'],
            [['rule_key'], 'string', 'max' => 100],
            [['rule_value'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rule_key'   => 'Rule Key',
            'rule_value' => 'Value',
            'description'=> 'Description',
            'updated_at' => 'Last Updated',
        ];
    }

    /**
     * Get a rule value by key. Returns $default if key not found.
     */
    public static function get(string $key, $default = null)
    {
        $rule = static::findOne(['rule_key' => $key]);
        return $rule ? $rule->rule_value : $default;
    }

    /**
     * Get a rule value as integer.
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int) static::get($key, $default);
    }

    /**
     * Set a rule value by key (upsert).
     */
    public static function set(string $key, string $value): bool
    {
        $rule = static::findOne(['rule_key' => $key]);
        if (!$rule) {
            $rule = new static();
            $rule->rule_key = $key;
        }
        $rule->rule_value = $value;
        return $rule->save();
    }
}
