<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Core\RequestController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\BlogRepositoryInterface;
use Carbon\Carbon;
use Log;
use Auth;

class BlogController extends RequestController
{

    private $blogRepositoryI;

    public function __construct(BlogRepositoryInterface $blogRepositoryI)
    {
        $this->blogRepositoryI = $blogRepositoryI;
    }

    /**
	 * Function to create update blog
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/blog/create-update
	 */
	public function createUpdate(Request $request)
	{
		try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
                'id' => 'numeric|exists:blogs'
            ]);
    
            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $blogData=[
                "name" => $inputs['name'],
                "description" => $inputs['description'],
            ];

            if($request->hasFile('image')) {
                $media = uploadFileToLocal($request->file('image'),constantConfig("images.blogs"));
                $blogData['media_id'] = $media['id'];
            }

            if(isset($inputs['id']) && !empty($inputs['id'])){
                $this->blogRepositoryI->update($blogData,["id" => $inputs['id'] ]);
            }else{
                $this->blogRepositoryI->create($blogData);
            }

			$response = $this->sendData($request, $this->success,[]);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}
	
    /**
	 * Function to get all blogs
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/blog/list
	 */
	public function blogList(Request $request)
	{

		try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'pagination.page' => 'required|numeric|min:1',
                'pagination.perpage' => 'required|numeric|min:1'
            ]);
    
            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

			$data = $this->blogRepositoryI->blogList($inputs);
			$response = $this->sendData($request, $this->success, $data);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}

    /**
     * Function to get blog detail
     * @param Request $request, $blogId
     * @return Response
     * @author  Rohit Singh
     * @api api/admin/blog/edit/{blogId}
     */
    public function edit(Request $request, $blogId)
    {
        try {
			$data = $this->blogRepositoryI->find([
                "id",
                "name",
                "description",
                \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
            ],
            [
                "id" => $blogId
            ]);

            $response = $this->sendData($request, $this->success, ['data' => $data]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }

        return $response;
    }

    /** Function to update blog status
     * @param Request $request
     * @return object
     * @author  Rohit Singh
     * @api api/admin/blog/status-update
     */
    public function updateStatus(Request $request)
    {
        try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:blogs',
                'status' => 'required|in:1,2'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $updateData = [
                "status" => $inputs['status']
            ];
            $this->blogRepositoryI->update($updateData,["id" => $inputs['id']]);
            
            $response = $this->sendJson($request, $this->success,["message"=>"Blog status changed successfully."]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
	}
    
}
