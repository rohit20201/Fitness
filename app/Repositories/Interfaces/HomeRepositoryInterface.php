<?php
namespace App\Repositories\Interfaces;

interface HomeRepositoryInterface extends BaseInterface
{
    public function create($data);
    public function update($update, $where);
    public function find($select, $where);
}