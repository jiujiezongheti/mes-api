<?php

use Phinx\Seed\AbstractSeed;

class AdminUserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $table = $this->table('user');
        $table->insert([
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'nickname' => '超级管理员',
            'status' => 1,
            'sort' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ])->save();
    }
}
