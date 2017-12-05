<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m171202_120011_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('users', [
            'id_user'  => $this->primaryKey(),
            'nickname' => $this->string(40)->notNull(),
            'ip'       => $this->string(20)->notNull(),
            'city'     => $this->string(50)->notNull(),
            'date'     => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
