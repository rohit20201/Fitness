<?php


namespace App\Repositories\Interfaces;


interface BaseInterface
{
    public function create($create);
    public function update($update,$where);

}
