<?php

use yii\db\Migration;

/**
 * Adds rejection_reason, rejected_by, checked_in_at, waitlisted_from
 * columns to reservations table.
 */
class m250629_000002_add_fields_to_reservations_table extends Migration
{
    public function safeUp()
    {
        // Reason admin/system gave when canceling
        $this->addColumn('{{%reservations}}', 'rejection_reason', $this->text()->null());

        // Who canceled: admin, system (auto-expire/conflict), or user themselves
        $this->execute("ALTER TABLE `reservations` ADD `rejected_by` ENUM('admin','system','user') NULL DEFAULT NULL");

        // QR check-in timestamp — null means not checked in
        $this->addColumn('{{%reservations}}', 'checked_in_at', $this->dateTime()->null());

        // If this reservation was created from a waitlist claim, store the waitlist id
        $this->addColumn('{{%reservations}}', 'waitlist_id', $this->integer()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%reservations}}', 'rejection_reason');
        $this->dropColumn('{{%reservations}}', 'rejected_by');
        $this->dropColumn('{{%reservations}}', 'checked_in_at');
        $this->dropColumn('{{%reservations}}', 'waitlist_id');
    }
}
