<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['site_id', 'name', 'username', 'email', 'password', 'ip_address', 'created_on', 'active', 'last_login', 'created_at', 'updated_at'];

    public function findByLogin(string $login): ?array
    {
        return $this->where('site_id', site_id())
            ->groupStart()
                ->where('email', $login)
                ->orWhere('username', $login)
            ->groupEnd()
            ->first();
    }
}
