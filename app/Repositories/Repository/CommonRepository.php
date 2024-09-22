<?php
namespace App\Repositories\Repository;

class CommonRepository
{
    public function tableAttributes($inputs,$tableDataCount,$tableData)
	{
		$dataArray = array();
		$dataArray['meta']['total'] = $tableDataCount;
		$dataArray['meta']['page'] = (int)$inputs['pagination']['page'];
		$dataArray['meta']['pages'] = (int)$inputs['pagination']['perpage'] > $tableDataCount ? 1 : ceil($tableDataCount/$inputs['pagination']['perpage']);
		$dataArray['meta']['perpage'] = (int)$inputs['pagination']['perpage'];
		$dataArray['meta']['sort'] = $inputs['sort']['sort'];
		$dataArray['meta']['field'] = $inputs['sort']['field'];
		$dataArray['data'] = $tableData;
		
		return $dataArray;
	}
}
