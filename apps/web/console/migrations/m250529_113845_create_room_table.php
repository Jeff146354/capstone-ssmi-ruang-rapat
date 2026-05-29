<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%room}}`.
 */
class m250529_113845_create_room_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%room}}', [
            'id' => $this->primaryKey(),
            'room' => $this->string()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'fr_img' => $this->string(), // nama file/foto ruangan
            'location' => $this->string(),
            'capacity' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%room}}');
    }
}
