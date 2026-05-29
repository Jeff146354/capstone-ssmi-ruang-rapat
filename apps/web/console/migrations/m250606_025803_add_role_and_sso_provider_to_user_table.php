<?php

use yii\db\Migration;

class m250606_025803_add_role_and_sso_provider_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user` ADD `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
        $this->execute("ALTER TABLE `user` ADD `sso_provider` ENUM('google', 'firebase') DEFAULT NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'role');
        $this->dropColumn('user', 'sso_provider');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250606_025803_add_role_and_sso_provider_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
