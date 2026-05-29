<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%schedule}}`.
 */
class m250529_114318_create_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%schedule}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'room_id' => $this->integer()->notNull(),
            'start_datetime' => $this->dateTime()->notNull(),
            'end_datetime' => $this->dateTime()->notNull(),
            'status_attendee' => "ENUM('Approved', 'Denied', 'Processed') NOT NULL DEFAULT 'Processed'",
            'document' => $this->string(),
            'affiliation' => $this->string(),
            'reason_of_use' => $this->text(),
        ]);

        // Foreign key to user table
        $this->addForeignKey(
            'fk-schedule-user_id',
            '{{%schedule}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Foreign key to room table
        $this->addForeignKey(
            'fk-schedule-room_id',
            '{{%schedule}}',
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
        $this->dropForeignKey('fk-schedule-user_id', '{{%schedule}}');
        $this->dropForeignKey('fk-schedule-room_id', '{{%schedule}}');
        $this->dropTable('{{%schedule}}');
    }
}
