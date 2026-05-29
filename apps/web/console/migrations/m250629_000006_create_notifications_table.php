<?php

use yii\db\Migration;

/**
 * Creates notifications table.
 */
class m250629_000006_create_notifications_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%notifications}}', [
            'id'             => $this->primaryKey(),
            'user_id'        => $this->integer()->notNull(),
            'type'           => "ENUM('reservation_approved','reservation_canceled','reservation_bumped','waitlist_available','strike_issued','suspension_issued') NOT NULL",
            'message'        => $this->text()->notNull(),
            'reservation_id' => $this->integer()->null(),
            'is_read'        => $this->boolean()->notNull()->defaultValue(false),
            'created_at'     => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-notifications-user_id',
            '{{%notifications}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-notifications-reservation_id',
            '{{%notifications}}', 'reservation_id',
            '{{%reservations}}', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-notifications-reservation_id', '{{%notifications}}');
        $this->dropForeignKey('fk-notifications-user_id', '{{%notifications}}');
        $this->dropTable('{{%notifications}}');
    }
}
