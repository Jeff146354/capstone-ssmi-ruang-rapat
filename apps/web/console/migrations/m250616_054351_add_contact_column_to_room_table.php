<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%room}}`.
 */
class m250616_054351_add_contact_column_to_room_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%room}}', 'contact', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%room}}', 'contact');
    }
}
