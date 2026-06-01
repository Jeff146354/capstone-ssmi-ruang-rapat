<?php

use yii\db\Migration;

/**
 * Adds soft-delete support to the room table.
 * Rooms are never hard-deleted — they are deactivated instead,
 * preserving all reservation history.
 */
class m250629_000007_add_is_active_to_room_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%room}}', 'is_active', $this->boolean()->notNull()->defaultValue(true));
        $this->createIndex('idx-room-is_active', '{{%room}}', 'is_active');
    }

    public function safeDown()
    {
        $this->dropIndex('idx-room-is_active', '{{%room}}');
        $this->dropColumn('{{%room}}', 'is_active');
    }
}
