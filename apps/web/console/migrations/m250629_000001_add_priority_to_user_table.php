<?php

use yii\db\Migration;

/**
 * Adds priority, booking_suspended_until, requires_manual_approval,
 * priority_boost_until columns to user table.
 */
class m250629_000001_add_priority_to_user_table extends Migration
{
    public function safeUp()
    {
        // Priority level: 1=mahasiswa, 2=staff, 3=dosen, 99=admin
        $this->addColumn('{{%user}}', 'priority', $this->integer()->notNull()->defaultValue(1));

        // Suspension: set to a future datetime when user is suspended
        $this->addColumn('{{%user}}', 'booking_suspended_until', $this->dateTime()->null());

        // Strike 3+: forces all future bookings to require manual admin approval
        $this->addColumn('{{%user}}', 'requires_manual_approval', $this->boolean()->notNull()->defaultValue(false));

        // Temporary priority boost after being bumped (expires after 7 days)
        $this->addColumn('{{%user}}', 'priority_boost_until', $this->dateTime()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'priority');
        $this->dropColumn('{{%user}}', 'booking_suspended_until');
        $this->dropColumn('{{%user}}', 'requires_manual_approval');
        $this->dropColumn('{{%user}}', 'priority_boost_until');
    }
}
