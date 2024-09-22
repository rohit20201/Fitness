<?php
use Carbon\Carbon;

function getTableData($table, $extra)
{

    $where = !empty($extra["where"]) ? $extra["where"] :  [];

    $orgTable = with(new $table)->getTable();

    if(empty($extra["na"]))
    {

        $where[$orgTable.".active"] = constantConfig("active");
    }

    $select = !empty($extra["select"]) ?  $extra["select"] : [];

    $is_table = !empty($extra["is_table"]) ?  $extra["is_table"] : [];
	
	$customInputs = !empty($extra["customInputs"]) ?  $extra["customInputs"] : [];

    $single = !empty($extra["single"]) ? $extra["single"] : 0;

    $count = !empty($extra["count"]) ? $extra["count"] : 0;

    $pluck = !empty($extra["pluck"]) ? $extra["pluck"] : 0;

    $query = $table::query();

    if(!empty($where))
    {
        $query->where($where);
    }

    $joins = !empty($extra["joins"]) ? $extra["joins"] : [];

    $limit = !empty($extra["limit"]) ? $extra["limit"] : 0;

    $offset  = !empty($extra["offset"]) ? $extra["offset"] : 0;

    $random = !empty($extra["random"]) ? $extra["random"] : 0;

    $orderBy = !empty($extra["order"]) ? $extra["order"] : [];

    $groupBy = !empty($extra["group"]) ? $extra["group"] : [];

    $having = !empty($extra["having"]) ? $extra["having"] : "";

    $whereNotIn  = !empty($extra["whereNotIn"]) ? $extra["whereNotIn"] : [];

    $whereIn  = !empty($extra["whereIn"]) ? $extra["whereIn"] : [];
	
    $orWhereIn  = !empty($extra["orWhereIn"]) ? $extra["orWhereIn"] : [];
	
    $orWhere  = !empty($extra["orWhere"]) ? $extra["orWhere"] : [];

    $whereDate  = !empty($extra["whereDate"]) ? $extra["whereDate"] : [];

    $whereNull  = !empty($extra["whereNull"]) ? $extra["whereNull"] : [];

    $whereNotNull  = !empty($extra["whereNotNull"]) ? $extra["whereNotNull"] : [];

    $whereRaw  = !empty($extra["whereRaw"]) ? $extra["whereRaw"] : "";

    $whereOperand = !empty($extra["whereOperand"]) ? $extra["whereOperand"] : [];
	
    $orderByRaw = !empty($extra["orderByRaw"]) ? $extra["orderByRaw"] : "";

    $customWhere= !empty($extra["customWhere"]) ? $extra["customWhere"] : "";

    $paginate = !empty($extra["paginate"]) ? $extra["paginate"] : 0;

    $distinct = !empty($extra["distinct"]) ? $extra["distinct"] : "";
	
    $whereRawSearch = !empty($extra["whereRawSearch"]) ? $extra["whereRawSearch"] : "";

    $whereHas = !empty($extra["whereHas"]) ? $extra["whereHas"] : [];

    $isUnion = !empty($extra["is_union"]) ? $extra["is_union"] : 0;

    $unionQuery = !empty($extra["unionQuery"]) ? $extra["unionQuery"] : "";
    
    $lockForUpdate = !empty($extra["lockForUpdate"]) ? $extra["lockForUpdate"] : false;

    if(!empty($joins))
    {

        foreach ($joins as $join)
        {

            $type = strtoupper(($join['type'] ?? ""));

            $joiningTable = !empty($join["alias"]) ? $join["alias"] : $join["table"];

            switch ($type){

                case "INNER":


                    $query->join($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        if(empty($join["na"]))
                        {
                            $internal->on($join['table'].".active","=", DB::raw(constantConfig("active")));
                        }

                        if(!empty($join["whereIn"]))
                        {
                            
                            foreach ($join["whereIn"] as $key => $value)
                            {
                                $internal->whereIn($key, $value);    
                            }
                            
                        }

                        if(!empty($join["whereRaw"]))
                        {

                            $internal->whereRaw($join["whereRaw"]["query"], $join["whereRaw"]["binding"]);

                        }

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    if(isset($value["concat"]))
									{
										$internal->on($key, $value["operand"], DB::raw("'$val'"));
									}
									else
									{
										$internal->on($key, $value["operand"], DB::raw("'.$val.'"));
									}
                                }
                                else if(isset($value["whereIn"]))
                                {
                                    $val = $value["value"];
                                    $internal->whereIn($key, $val);
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }
                            }
                        }


                    });

                    break;

                case "LEFT":

                    $query->leftJoin($joiningTable, function ($internal) use ($join){
						
						if(isset($join["findSet"]))
						{
							$condition = $join["condition"];
							$internal->on(DB::raw("$condition"),">",DB::raw("'0'"));
						}
						else
						{
							$internal->on($join["left_condition"],"=",$join["right_condition"]);
						}
                        

                        if(empty($join["na"]))
                        {
                            $internal->on($join['table'].".active","=", DB::raw(constantConfig("active")));
                        }

                        if(!empty($join["whereIn"]))
                        {
                            
                            foreach ($join["whereIn"] as $key => $value)
                            {
                                $internal->whereIn($key, $value);    
                            }
                            
                        }
                        if(!empty($join["whereNotIn"]))
                        {
                            
                            foreach ($join["whereNotIn"] as $key => $value)
                            {
                                $internal->whereNotIn($key, $value);    
                            }
                            
                        } 

                        if(!empty($join["whereRaw"]))
                        {

                            $internal->whereRaw($join["whereRaw"]["query"], $join["whereRaw"]["binding"]);

                        }


                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {
                                    $val = $value["value"];
									//added case for not concating the dots with the value
									if(isset($value["concat"]))
									{
										$internal->on($key, $value["operand"], DB::raw("'$val'"));
									}
									else
									{
										$internal->on($key, $value["operand"], DB::raw("'.$val.'"));
									}
                                } 
                                else if(isset($value["whereIn"]))
                                {
                                        $val = $value["value"];
                                        $internal->whereIn($key, $val);
                                }  
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }


                            }

                        }
                       
                    });

                    break;

                case "RIGHT":

                    $query->rightJoin($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        $internal->on($join['table'].".active", DB::raw(constantConfig("active")));

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    if(isset($value["concat"]))
									{
										$internal->on($key, $value["operand"], DB::raw("'$val'"));
									}
									else
									{
										$internal->on($key, $value["operand"], DB::raw("'.$val.'"));
									}
                                }
                                else if(isset($value["whereIn"]))
                                {
                                    $val = $value["value"];
                                    $internal->whereIn($key, $val);
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }


                            }

                        }

                    });

                    break;
                case 'RAWJOIN':
                      
                        $query->leftJoin((DB::raw($joiningTable)), function ($internal) use ($join){
    
                            $internal->on($join["left_condition"],"=",$join["right_condition"]);
                            if(!empty($join["conditions"]))
                            {
    
                                foreach ($join["conditions"] as $key => $value)
                                {
    
                                    if(!empty($value["operand"]))
                                    {
                                        $val = $value["value"];
                                        //added case for not concating the dots with the value
                                        if(isset($value["concat"]))
                                        {
                                            $internal->on($key, $value["operand"], DB::raw("'$val'"));
                                        }
                                        else
                                        {
                                            $internal->on($key, $value["operand"], DB::raw("'.$val.'"));
                                        }
                                    } 
                                    else if(isset($value["whereIn"]))
                                    {
                                            $val = $value["value"];
                                            $internal->whereIn($key, $val);
                                    }  
                                    else
                                    {
    
                                        $internal->on($key, DB::raw($value));
                                    }
    
    
                                }
    
                            }
                        });
    
                    
                    break;
                default:

                    $query->join($joiningTable, function ($internal) use ($join){

                        $internal->on($join["left_condition"],"=",$join["right_condition"]);
                        $internal->on($join['table'].".active", DB::raw(constantConfig("active")));

                        if(!empty($join["conditions"]))
                        {

                            foreach ($join["conditions"] as $key => $value)
                            {

                                if(!empty($value["operand"]))
                                {

                                    $val = $value["value"];

                                    if(isset($value["concat"]))
									{
										$internal->on($key, $value["operand"], DB::raw("'$val'"));
									}
									else
									{
										$internal->on($key, $value["operand"], DB::raw("'.$val.'"));
									}
                                }
                                else if(isset($value["whereIn"]))
                                {
                                    $val = $value["value"];
                                    $internal->whereIn($key, $val);
                                }
                                else
                                {

                                    $internal->on($key, DB::raw($value));
                                }


                            }

                        }

                    });

                    break;
            }


        }


    }

    if(!empty($whereNotIn))
    {

        foreach ($whereNotIn as $key => $not)
        {

            $query->whereNotIn($key, $not);

        }

    }

    if(!empty($whereIn))
    {

        foreach ($whereIn as $key => $in)
        {

            $query->whereIn($key, $in);

        }

    }
	
	if(!empty($orWhereIn))
    {
		$query->where(function ($q) use($orWhereIn,$whereRawSearch) {
			$countD=0;
			foreach ($orWhereIn as $key => $in)
			{
				if($countD > 0)
					$q->whereIn($key, $in,'or');
				else
					$q->whereIn($key, $in);
				
				$countD++;
			}
			if($whereRawSearch!="")
				$q->whereRaw($whereRawSearch);
		});
    }
	
	if(empty($orWhereIn) && $whereRawSearch!="")
    {
		$query->where(function ($q) use($whereRawSearch) {
			$q->whereRaw($whereRawSearch);
		});
	}
	
	if(!empty($orWhere))
    {
		$query->where(function ($q) use($orWhere) {
			foreach ($orWhere as $key => $inner)
			{
				$i=0;
				foreach($inner as $in)
				{
					if($i > 0)
						$q->orWhere($key, $in);
					else
						$q->where($key, $in);
					$i++;
				}
			}
		});
    }

    if(!empty($whereOperand))
    {

        foreach ($whereOperand as $where)
        {
			if(isset($where['whereContition']) && $where['whereContition'] == "or")
			{
				//create seperate function for this logic
				$query->where(function ($q) use($where) {
					$q->Where($where["where"]["column"], $where["where"]["value"]);
					$q->whereIn($where["whereIn"]["column"], $where["whereIn"]["value"]);
					$q->orWhere($where["column"], $where["operand"], $where["value"]);
				});
			}
            elseif(isset($where['whereContition']) && $where['whereContition'] == "multiple")
			{
				//create seperate function for this logic
				$query->where(function ($q) use($where) {
                    if(!empty($where["where"])) {
                        foreach($where["where"] as $whr) {
                            $q->where($whr["column"], $whr["value"]);
                        }
                    }
                    if(!empty($where["orWhere"])) {
                        foreach($where["orWhere"] as $key=>$orWhr) {
                            if($key > 0 || (isset($orWhr['condition']) && $orWhr['condition'] == "or")) {
                                $q->orWhere($orWhr["column"], $orWhr["value"]);
                            } else {
                                $q->where($orWhr["column"], $orWhr["value"]);
                            }
                        }
                    }
                    if(!empty($where["whereIn"])) {
                        foreach($where["whereIn"] as $whrIn) {
                            $q->whereIn($whrIn["column"], $whrIn["value"]);
                        }
                    }
                    if(!empty($where["orWhereIn"])) {
                        foreach($where["orWhereIn"] as $key=>$orWhrIn) {
                            if($key > 0 || (isset($orWhrIn['condition']) && $orWhrIn['condition'] == "or")) {
                                $q->orWhereIn($orWhrIn["column"], $orWhrIn["value"]);
                            } else {
                                $q->whereIn($orWhrIn["column"], $orWhrIn["value"]);
                            }
                        }
                    }
                    if(!empty($where["whereNotIn"])) {
                        foreach($where["whereNotIn"] as $whrNotIn) {
                            $q->whereNotIn($whrNotIn["column"], $whrNotIn["value"]);
                        }
                    }
                    if(!empty($where["orWhereNotIn"])) {
                        foreach($where["orWhereNotIn"] as $key=>$orWhrNotIn) {
                            if($key > 0 || (isset($orWhrNotIn['condition']) && $orWhrNotIn['condition'] == "or")) {
                                $q->orWhereNotIn($orWhrNotIn["column"], $orWhrNotIn["value"]);
                            } else {
                                $q->whereNotIn($orWhrNotIn["column"], $orWhrNotIn["value"]);
                            }
                        }
                    }
                    if(!empty($where["whereNotNull"])) {
                        foreach($where["whereNotNull"] as $whrNotNull) {
                            $q->whereNotNull($whrNotNull);
                        }
                    }
                    if(!empty($where["orWhereNotNull"])) {
                        foreach($where["orWhereNotNull"] as $orWhrNotNull) {
                            $q->orWhereNotNull($orWhrNotNull);
                        }
                    }
                    if(!empty($where["whereRaw"])) {
                        $q->whereRaw($where["whereRaw"]);
                    }
                    if(!empty($where["orWhereRaw"])) {
                        $q->orWhereRaw($where["orWhereRaw"]);
                    }
				});
			}
			else
			{
				$query->Where($where["column"], $where["operand"], $where["value"]);
			}
        }
    }

    if(!empty($whereNull))
    {

        foreach ($whereNull as $null)
        {

            $query->whereNull($null);

        }

    }
    /** whereHas to bind user role and user permission with query 
	 * @author  Ravinder Singh
     */
    if(!empty($whereHas))
    {
        foreach ($whereHas as $table=>$columns)
        {
            foreach ($columns as $column)
            {
                if(isset($column['permission']) &&  $column['permission']==isActive()){
                    /* i.e. user permission equal to some permissoin (name='permission_name')*/
                    $query->whereHas($table, function($q) use($column){
                        $q->whereHas('permissions', function($q1) use($column){
                        $q1->where($column['name'], $column["operand"], $column['value']);
                        });
                    });
                }else{
                    /* i.e. user role equal to some role (name='role_name')*/
                    $query->whereHas($table, function($q) use($column){
                        $q->where($column['name'], $column["operand"], $column['value']);
                    });
                }
            }
            
        }
    }

    if(!empty($whereNotNull))
    {

        foreach ($whereNotNull as $null)
        {

            $query->whereNotNull($null);

        }

    }

	if(!empty($whereDate))
    {
		foreach ($whereDate as $key=>$val)
        {
			$query->whereDate($key,$val);

		}
    }

    if(!empty($customWhere))
    {

        $query->where($customWhere);

    }

	if(!empty($distinct))
    {

        $query->distinct();

    }

    if(!empty($whereRaw))
    {

        $query->whereRaw($whereRaw);

    }

    if(!empty($limit))
    {

        $query->limit($limit);

    }

    if(!empty($offset))
    {

        $query->offset($offset);

    }
	
	if(!empty($orderByRaw))
    {
		$query->orderByRaw($orderByRaw);
    }
	
    if(!empty($orderBy)) {

        foreach ($orderBy as $column => $order)
        {

            $query->orderBy($column, $order);

        }

    }

    if(!empty($random))
    {

        $query->inRandomOrder();

    }

    if(!empty($groupBy)) {

        foreach ($groupBy as $group)
        {

            $query->groupBy($group);

        }

    }

    if(!empty($unionQuery)) {
        $query->union($unionQuery);
    }

    // for KT table
    if(!empty($is_table)) {
        //for sorting
        if(isset($is_table['inputs']['sort']))
        {
            if(isset($is_table['prefix']) && in_array($is_table['inputs']['sort']['field'],array_keys($is_table['prefix'])))
            {
                $is_table['inputs']['sort']['field'] = $is_table['prefix'][$is_table['inputs']['sort']['field']];
            }
            $query->orderBy($is_table['inputs']['sort']['field'], $is_table['inputs']['sort']['sort']);
        }

        //for search and query string
        if(isset($is_table['inputs']['query']))
        {
            foreach($is_table['inputs']['query'] as $column=>$value)
            {
               if(isset($is_table['prefix']) && in_array($column,array_keys($is_table['prefix'])))
                {
                    $column = $is_table['prefix'][$column];
                }
                if($column != 'generalSearch')
                {
					if(is_array($column) && $value != "")
					{
						$query->where(function($q) use($column, $select,$value) {
							$ij=0;
							foreach($column as $search)
							{
								if($ij>0)
									$q->orWhere($search,$value);
								else
									$q->where($search,$value);
								
								$ij++;
							}
						});
					}
					else
					{
						if(isset($is_table['notNull']['status']) && !empty($is_table['notNull']['status']))
						{
							
							if($is_table['notNull']['status'] == 2)
							{
								$value !== "" ? $query->whereNull($column) : '';
								$value !== "" ? $query->orWhere($column, '=', '') : '';
							
							}
							else
							{
								$value !== "" ? $query->where($column, '<>', '') : '';
							}
						}
						else
						{
							$date = explode('-', ($value ?? ""));
							if(isset($date) && count($date) == 2 && strtotime($date[0]) !== false)
							{
								$startDate = Carbon::parse($date[0]);
								$startDate->format("Y-m-d");
								
								$endDate = Carbon::parse($date[1]);
								$endDate->format("Y-m-d");
								
								$value !== "" ? $query->where($column, '>=', "$startDate") : '';
								$value !== "" ? $query->where($column, '<=', "$endDate") : '';
							}
							else
							{
								$value !== "" ? $query->where($column,$value) : '';
							}
						}
						
					}
                }
                else
                {
					if($value != "")
                    {
                        $query->where(function($q) use($is_table, $select,$value) {
                            if(isset($is_table['more_search']))
                            {
                                foreach($is_table['more_search'] as $more_search)
                                {
                                    $q->orWhere($more_search, 'LIKE', "%{$value}%");
                                }
                            }
							if(isset($is_table['more_search_intval']))
                            {
                                $intValue = intval($value);
                                if($intValue > 0){
                                    foreach($is_table['more_search_intval'] as $more_search)
                                    {
                                        $q->orWhere($more_search, 'LIKE', "%{$intValue}%");
                                    }
                                }                               
                            }
                            if(isset($is_table['whereRawSearch']))
                            {
								$q->orWhereRaw($is_table['whereRawSearch']);
                            }
                            foreach($select as $col)
                            {
                                if(!is_object($col))
                                {
									if (strpos($col, ' as ') === false) {
										$q->orWhere($col, 'LIKE', "%{$value}%");
									}
                                }
                            }
                        });
                    }
                }
            }
        }

        //for pagination
        if(isset($is_table['inputs']['pagination']['perpage']) && !isset($is_table['all']) && empty($isUnion))
        {
            if($is_table['inputs']['pagination']['page'] == 1)
            {
                $query->take($is_table['inputs']['pagination']['perpage']);
            }
            else
            {
                $take = $is_table['inputs']['pagination']['perpage'];
                $skip = ($is_table['inputs']['pagination']['perpage'] * $is_table['inputs']['pagination']['page']) - $is_table['inputs']['pagination']['perpage'];
                $query->take($take)->skip($skip);
            }
        }
    }
    // for KT table

    if(!empty($having))
    {

        $query->havingRaw($having);

    }

    if(!empty($lockForUpdate))
    {
        $query->lockForUpdate();
    }

    if(!empty($single))
    {

        $response = $query->first($select);

    }
    elseif(!empty($count))
    {
        $response = $query->get($select)->count();

    }
    elseif(!empty($paginate))
    {

        $response = $query->select($select)->paginate($paginate);

    }
	elseif(!empty($pluck))
    {

        $response = $query->pluck($select[0]);

    }
	elseif(isset($extra['is_table']['union']))
    {

        $response = $query->select($select);

    }
	elseif(!empty($isUnion))
    {

        $response = $query->select($select);

    }	
    else
    {

        $response = $query->get($select);

    }

    return $response;

}

function insertData($table, $extra)
{


    if(empty($extra["na"]))
    {

        if((empty($extra["data"][0])))
        {

            $extra["data"]['active'] = isActive();
			if(!isset($extra["data"]['created_utc']) && empty($extra["data"]['created_utc']))
			{
              $extra["data"][$table::CREATED_AT] = currentTime();
			}

        }

        if((!empty($extra["data"][0]) && !is_array($extra["data"][0])))
        {

            $extra["data"] = [$extra["data"]];

            array_walk($extra["data"], $f = function (&$value, $key) use ($table) {

                $value['active'] = isActive();
                $value[$table::CREATED_AT] = currentTime();

            });

        }

        if((!empty($extra["data"][0]) && is_array($extra["data"][0]))) {

            array_walk($extra["data"], $f = function (&$value, $key) use ($table) {

                $value['active'] = isActive();
                $value[$table::CREATED_AT] = currentTime();

            });

        }

    }

    if(!empty($extra["id"]) && !isset($extra["data"][0]))
    {

        $status = $table::insertGetId($extra["data"]);

    }
    else
    {

        $status =  $table::insert($extra["data"]);

    }

    unset($value);

    return $status;

}

function updateData($table, $extra)
{

    $where = !empty($extra["where"]) ? $extra["where"] : [];

    $whereIn = !empty($extra["whereIn"]) ? $extra["whereIn"] : [];
	
    $whereNotIn = !empty($extra["whereNotIn"]) ? $extra["whereNotIn"] : [];

    $update = !empty($extra["update"]) ? $extra["update"]  : [];

    $update[$table::UPDATED_AT] = currentTime();


    if(empty($extra["na"]))
    {

        $where["active"] = constantConfig("active");
    }

    $query = $table::where($where);

    if(!empty($whereIn))
    {

        foreach ($whereIn as $key => $val)
        {

            $query->whereIn($key, $val);

        }


    }
	
	if(!empty($whereNotIn))
    {
		
        foreach ($whereNotIn as $key => $val)
        {

            $query->whereNotIn($key, $val);

        }


    }

    $status = $query->update($update);

    return $status;

}
