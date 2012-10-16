<?php

class m120927_081429_add_comment_table extends CDbMigration
{

    public function up()
    {
        $this->createTable('rm_comment', array(
            'id' => 'pk',
            'user_id' => 'INT NOT NULL',
            'comment' => 'VARCHAR(500) NOT NULL',
            'create_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'trash' => 'TINYINT(1)',
        ));
    }

    public function down()
    {
        $this->dropTable('rm_comment');
    }

}