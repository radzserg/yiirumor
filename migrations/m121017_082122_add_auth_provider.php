<?php

class m121017_082122_add_auth_provider extends CDbMigration
{
	public function up()
	{
        $this->createTable('rm_auth_provider', array(
            'id' => 'pk',
            'code' => 'VARCHAR(10) NOT NULL',
            'comment' => 'VARCHAR(500) NOT NULL',
            'create_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'active' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'trash' => 'TINYINT(1) NOT NULL DEFAULT 0',
        ));

        $this->addForeignKey('fk_rm_auth_provider_x_rm_user', 'rm_user', 'auth_provider_id', 'rm_auth_provider', 'id');
	}

	public function down()
	{
        $this->dropForeignKey('fk_rm_auth_provider_x_rm_user', 'rm_user');
		$this->dropTable('rm_auth_provider');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}