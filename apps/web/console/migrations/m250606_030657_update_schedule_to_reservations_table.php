<?php

use yii\db\Migration;

class m250606_030657_update_schedule_to_reservations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Rename table
        $this->renameTable('{{%schedule}}', '{{%reservations}}');

        // Drop old foreign keys
        $this->dropForeignKey('fk-schedule-user_id', '{{%reservations}}');
        $this->dropForeignKey('fk-schedule-room_id', '{{%reservations}}');

        // Drop and modify columns
        $this->dropColumn('{{%reservations}}', 'start_datetime');
        $this->dropColumn('{{%reservations}}', 'end_datetime');
        $this->dropColumn('{{%reservations}}', 'status_attendee');

        // Modify existing columns
        $this->addColumn('{{%reservations}}', 'date', $this->date());
        $this->addColumn('{{%reservations}}', 'start_time', $this->time());
        $this->addColumn('{{%reservations}}', 'end_time', $this->time());
        $this->addColumn('{{%reservations}}', 'status', "ENUM('pending', 'approved', 'canceled') DEFAULT 'pending'");
        $this->addColumn('{{%reservations}}', 'created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

        // Re-add foreign key
        $this->addForeignKey(
            'fk-reservations-user_id',
            '{{%reservations}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-reservations-room_id',
            '{{%reservations}}',
            'room_id',
            '{{%room}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250606_030657_update_schedule_to_reservations_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250606_030657_update_schedule_to_reservations_table cannot be reverted.\n";

        return false;
    }
    */
}
