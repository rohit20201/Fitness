<?php
namespace App\Repositories\Repository;

use Auth;
use App\Models\WebsiteTemplate;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use App\Repositories\Repository\CommonRepository;
use Illuminate\Support\Collection;

class HomeRepository implements HomeRepositoryInterface
{
	private $model = WebsiteTemplate::class;

    private $commonRepository;

    public function __construct(commonRepository $CommonRepository)
    {
        $this->commonRepository = $CommonRepository;
    }
	
	public function create($create)
    {
        return insertData($this->model,[
           "data" => $create,
            "id" => isActive()
        ]);
    }

    public function update($update, $where)
    {
        updateData($this->model,[
            "update" => $update,
            "where" => $where
        ]);
    }
	
	public function find($select, $where)
    {
        return getTableData($this->model,[
            "select" => $select,
            "where" => $where,
            "single" => isActive()
        ]);
    }
}
