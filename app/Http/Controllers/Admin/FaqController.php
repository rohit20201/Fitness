<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Core\RequestController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\FaqRepositoryInterface;
use Carbon\Carbon;
use Log;
use Auth;

class FaqController extends RequestController
{

    private $faqRepositoryI;

    public function __construct(FaqRepositoryInterface $faqRepositoryI)
    {
        $this->faqRepositoryI = $faqRepositoryI;
    }

    /**
	 * Function to create update faq
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/faq/create-update
	 */
	public function createUpdate(Request $request)
	{
		try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'id' => 'numeric|exists:faqs'
            ]);
    
            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $faqData=[
                "name" => $inputs['name'],
                "description" => $inputs['description'],
            ];

            if(isset($inputs['id']) && !empty($inputs['id'])){
                $this->faqRepositoryI->update($faqData,["id" => $inputs['id'] ]);
            }else{
                $this->faqRepositoryI->create($faqData);
            }

			$response = $this->sendData($request, $this->success,[]);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}
	
    /**
	 * Function to get all faqs
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/faq/list
	 */
	public function faqList(Request $request)
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

			$data = $this->faqRepositoryI->faqList($inputs);
			$response = $this->sendData($request, $this->success, $data);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}

    /**
     * Function to get faq detail
     * @param Request $request, $faqId
     * @return Response
     * @author  Rohit Singh
     * @api api/admin/faq/edit/{faqId}
     */
    public function edit(Request $request, $faqId)
    {
        try {
			$data = $this->faqRepositoryI->find([
                "id",
                "name",
                "description",
                \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
            ],
            [
                "id" => $faqId
            ]);

            $response = $this->sendData($request, $this->success, ['data' => $data]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }

        return $response;
    }

    /** Function to update faq status
     * @param Request $request
     * @return object
     * @author  Rohit Singh
     * @api api/admin/faq/status-update
     */
    public function updateStatus(Request $request)
    {
        try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:faqs',
                'status' => 'required|in:1,2'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $updateData = [
                "status" => $inputs['status']
            ];
            $this->faqRepositoryI->update($updateData,["id" => $inputs['id']]);
            
            $response = $this->sendJson($request, $this->success,["message"=>"Faq status changed successfully."]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
	}


}
