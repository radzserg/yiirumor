<?php

class m121017_071415_add_user_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('rm_user', array(
            'id' => 'pk',
            'auth_provider_id' => 'INT NOT NULL',
            'username' => 'VARCHAR(255) NOT NULL',
            'info' => 'TEXT',
            'create_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'trash' => 'TINYINT(1)',
        ));

        $this->execute("TRUNCATE rm_comment");

        $this->addForeignKey('fk_rm_user_x_rm_comment', 'rm_comment', 'user_id', 'rm_user', 'id');
	}

	public function down()
	{
        $this->dropForeignKey('fk_rm_user_x_rm_comment', 'rm_comment');
        $this->dropTable('rm_user');
	}
}