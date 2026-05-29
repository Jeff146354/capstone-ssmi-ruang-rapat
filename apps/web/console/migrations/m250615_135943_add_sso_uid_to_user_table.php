<?php

use yii\db\Migration;

class m250615_135943_add_sso_uid_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'sso_uid', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'sso_uid');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250615_135943_add_sso_uid_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
