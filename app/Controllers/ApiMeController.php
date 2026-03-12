<?php

namespace App\Controllers;

class ApiMeController extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->currentUser) {
            return $this->response->setJSON(['logged_in' => false]);
        }

        return $this->response->setJSON([
            'logged_in' => true,
            'id'        => $this->currentUser['id'],
            'name'      => $this->currentUser['name'],
            'username'  => $this->currentUser['username'],
        ]);
    }
}
