<?php

use yii\db\Migration;

/**
 * Creates user_strikes table for the no-show / behavior strike system.
 */
class m250629_000004_create_user_strikes_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user_strikes}}', [
            'id'             => $this->primaryKey(),
            'user_id'        => $this->integer()->notNull(),
            'reservation_id' => $this->integer()->null(),
            'reason'         => "ENUM('no_show','late_cancel','damage') NOT NULL",
            'notes'          => $this->text()->null(),
            'created_at'     => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'expires_at'     => $this->dateTime()->null(), // null = permanent until admin clears
        ]);

        $this->addForeignKey(
            'fk-user_strikes-user_id',
            '{{%user_strikes}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_strikes-reservation_id',
            '{{%user_strikes}}', 'reservation_id',
            '{{%reservations}}', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_strikes-reservation_id', '{{%user_strikes}}');
        $this->dropForeignKey('fk-user_strikes-user_id', '{{%user_strikes}}');
        $this->dropTable('{{%user_strikes}}');
    }
}
