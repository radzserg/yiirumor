<?php

class m121018_112004_add_vk_auth_provider extends CDbMigration
{

    public function up()
    {
        $this->createTable('rm_auth_provider_vk', array(
            'id' => 'pk',
            'user_id' => 'INT NOT NULL',
            'vk_id' => 'BIGINT NOT NULL',
            'access_token' => 'VARCHAR(255)',
            'token_expires' => 'DATETIME',
            'user_data' => 'TEXT',
        ));

        $this->addForeignKey('fk_rm_auth_provider_vk_x_rm_user', 'rm_auth_provider_vk', 'user_id', 'rm_user', 'id');
        $this->createIndex('ix_rm_auth_provider_vk_vk_id', 'rm_auth_provider_vk', 'vk_id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_rm_auth_provider_vk_x_rm_user', 'rm_auth_provider_vk');
        $this->dropTable('rm_auth_provider_vk');
    }

}