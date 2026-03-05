<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['name', 'username', 'email', 'password', 'ip_address', 'created_on', 'active', 'last_login', 'created_at', 'updated_at'];

    public function findByLogin(string $login): ?array
    {
        return $this->groupStart()
            ->where('email', $login)
            ->orWhere('username', $login)
            ->groupEnd()
            ->first();
    }
}
