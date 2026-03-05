<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    public function getAllCategories(): array
    {
        return $this->where('slug <>', '')
            ->where('LENGTH(slug) >', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
