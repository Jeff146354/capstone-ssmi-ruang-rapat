<?php

use yii\db\Migration;

/**
 * Creates booking_rules table for admin-configurable policies.
 */
class m250629_000003_create_booking_rules_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%booking_rules}}', [
            'id'          => $this->primaryKey(),
            'rule_key'    => $this->string(100)->notNull()->unique(),
            'rule_value'  => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
            'updated_at'  => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Seed default values
        $this->batchInsert('{{%booking_rules}}', ['rule_key', 'rule_value', 'description'], [
            ['max_duration_hours',       '4',     'Maximum booking duration in hours'],
            ['min_duration_minutes',     '30',    'Minimum booking duration in minutes'],
            ['max_advance_days',         '30',    'How many days ahead a booking can be made'],
            ['cancellation_hours_before','24',    'Minimum hours before booking that cancellation is allowed'],
            ['max_pending_per_user',     '5',     'Maximum number of pending reservations per user'],
            ['operating_hours_start',    '07:00', 'Earliest allowed booking start time (HH:MM)'],
            ['operating_hours_end',      '21:00', 'Latest allowed booking end time (HH:MM)'],
            ['buffer_minutes_between',   '15',    'Required gap in minutes between consecutive bookings in the same room'],
            ['pending_expire_hours',     '48',    'Hours before booking date that an unreviewed pending reservation is auto-canceled'],
            ['strike_suspend_days',      '3',     'Days a user is suspended after Strike 2'],
            ['waitlist_claim_days',      '3',     'Days a waitlisted user has to claim an opened slot'],
            ['priority_boost_days',      '7',     'Days a priority boost lasts after a user is bumped'],
            ['enable_noshow_strikes',    '0',     'Set to 1 to enable automatic no-show strikes. Keep 0 until QR check-in is fully implemented.'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%booking_rules}}');
    }
}
