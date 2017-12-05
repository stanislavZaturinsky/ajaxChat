<?php

use yii\db\Migration;

/**
 * Handles the creation of table `messages`.
 */
class m171202_120848_create_messages_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('messages', [
            'id_message' => $this->primaryKey(),
            'id_user'    => $this->integer(11)->notNull(),
            'text'       => $this->text()->notNull(),
            'date'       => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->addForeignKey(
            'users_id_fk',
            'messages',
            'id_user',
            'users',
            'id_user',
            'RESTRICT'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('messages');
    }
}
