<?php
namespace App\Repositories\Interfaces;

interface FaqRepositoryInterface extends BaseInterface
{
    public function create($data);
    public function update($update, $where);
    public function find($select, $where);
    public function faqList($inputs);
}