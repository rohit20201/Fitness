<?php
namespace App\Repositories\Interfaces;

interface TestimonialRepositoryInterface extends BaseInterface
{
    public function create($data);
    public function update($update, $where);
    public function find($select, $where);
    public function getAll($select, $where);
    public function testimonialList($inputs);
}