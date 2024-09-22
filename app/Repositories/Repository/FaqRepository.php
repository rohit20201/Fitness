<?php
namespace App\Repositories\Repository;

use Auth;
use App\Models\Faq;
use App\Repositories\Interfaces\FaqRepositoryInterface;
use App\Repositories\Repository\CommonRepository;
use Illuminate\Support\Collection;

class FaqRepository implements FaqRepositoryInterface
{
	private $model = Faq::class;

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

    public function faqList($inputs)
	{
        $inputs['sort']['sort'] = isset($inputs['sort']['sort']) ? $inputs['sort']['sort'] : "asc";
        $inputs['sort']['field'] = isset($inputs['sort']['field']) ? $inputs['sort']['field'] : "faqs.id";

        $where = [];
        $select= [
            "id",
            "name",
            "description",
            \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
        ];
       
		$tableDataCount = getTableData($this->model, [
            "select" => $select,
            "is_table"=>[
                "inputs" => $inputs,
                "all" => isActive(),
            ],
            "where"=>$where,
            'count'=>isActive()
        ]);

        $tableData = getTableData($this->model, [
            "select" => $select,
            "where"=>$where,
            "is_table"=>[
                "inputs" => $inputs
            ],
        ]);
       
        $dataArray = $this->commonRepository->tableAttributes($inputs,$tableDataCount,$tableData);
        return $dataArray;
	}
}
