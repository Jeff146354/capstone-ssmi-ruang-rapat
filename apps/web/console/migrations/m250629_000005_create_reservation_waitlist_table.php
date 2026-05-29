<?php

use yii\db\Migration;

/**
 * Creates reservation_waitlist table.
 */
class m250629_000005_create_reservation_waitlist_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%reservation_waitlist}}', [
            'id'          => $this->primaryKey(),
            'user_id'     => $this->integer()->notNull(),
            'room_id'     => $this->integer()->notNull(),
            'date'        => $this->date()->notNull(),
            'start_time'  => $this->time()->notNull(),
            'end_time'    => $this->time()->notNull(),
            'status'      => "ENUM('waiting','notified','claimed','expired') NOT NULL DEFAULT 'waiting'",
            'notified_at' => $this->dateTime()->null(),  // when user was told a slot opened
            'created_at'  => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-waitlist-user_id',
            '{{%reservation_waitlist}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-waitlist-room_id',
            '{{%reservation_waitlist}}', 'room_id',
            '{{%room}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-waitlist-room_id', '{{%reservation_waitlist}}');
        $this->dropForeignKey('fk-waitlist-user_id', '{{%reservation_waitlist}}');
        $this->dropTable('{{%reservation_waitlist}}');
    }
}
