<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Core\RequestController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\BannerRepositoryInterface;
use App\Repositories\Interfaces\TestimonialRepositoryInterface;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use Carbon\Carbon;
use Log;
use Auth;

class HomeController extends RequestController
{
    private $bannerRepositoryI;
    private $testimonialRepositoryI;
    private $homeRepositoryI;

    public function __construct(BannerRepositoryInterface $bannerRepositoryI,
                                TestimonialRepositoryInterface $testimonialRepositoryI,
                                HomeRepositoryInterface $homeRepositoryI)                           
    {
        $this->bannerRepositoryI = $bannerRepositoryI;
        $this->testimonialRepositoryI = $testimonialRepositoryI;
        $this->homeRepositoryI = $homeRepositoryI;
    }

    /**
	 * Function to create update banner
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/banner/create-update
	 */
	public function createUpdateBanner(Request $request)
	{
		try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
                'id' => 'numeric|exists:banners'
            ]);
    
            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $bannerData=[
                "name" => $inputs['name'],
                "description" => $inputs['description'],
            ];

            if($request->hasFile('image')) {
                $media = uploadFileToLocal($request->file('image'),constantConfig("images.banners"));
                $bannerData['media_id'] = $media['id'];
            }

            if(isset($inputs['id']) && !empty($inputs['id'])){
                $this->bannerRepositoryI->update($bannerData,["id" => $inputs['id'] ]);
            }else{
                $this->bannerRepositoryI->create($bannerData);
            }

			$response = $this->sendData($request, $this->success,[]);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}
	
    /**
	 * Function to get all banners
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/banner/list
	 */
	public function bannerList(Request $request)
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

			$data = $this->bannerRepositoryI->bannerList($inputs);
			$response = $this->sendData($request, $this->success, $data);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}

    /**
     * Function to get banner detail
     * @param Request $request, $bannerId
     * @return Response
     * @author  Rohit Singh
     * @api api/admin/banner/edit/{bannerId}
     */
    public function editBanner(Request $request, $bannerId)
    {
        try {
			$data = $this->bannerRepositoryI->find([
                "id",
                "name",
                "description",
                \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
            ],
            [
                "id" => $bannerId
            ]);

            $response = $this->sendData($request, $this->success, ['data' => $data]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }

        return $response;
    }

    /** Function to update banner status
     * @param Request $request
     * @return object
     * @author  Rohit Singh
     * @api api/admin/banner/status-update
     */
    public function updateBannerStatus(Request $request)
    {
        try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:banners',
                'status' => 'required|in:1,2'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $updateData = [
                "status" => $inputs['status']
            ];
            $this->bannerRepositoryI->update($updateData,["id" => $inputs['id']]);
            
            $response = $this->sendJson($request, $this->success,["message"=>"Banner status changed successfully."]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
	}

    /**
	 * Function to create update testimonial
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/testimonial/create-update
	 */
	public function createUpdateTestimonial(Request $request)
	{
		try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
                'id' => 'numeric|exists:testimonials'
            ]);
    
            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $testimonialData=[
                "name" => $inputs['name'],
                "description" => $inputs['description'],
            ];

            if($request->hasFile('image')) {
                $media = uploadFileToLocal($request->file('image'),constantConfig("images.testimonials"));
                $testimonialData['media_id'] = $media['id'];
            }

            if(isset($inputs['id']) && !empty($inputs['id'])){
                $this->testimonialRepositoryI->update($testimonialData,["id" => $inputs['id'] ]);
            }else{
                $this->testimonialRepositoryI->create($testimonialData);
            }

			$response = $this->sendData($request, $this->success,[]);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}
	
    /**
	 * Function to get all testimonials
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/testimonial/list
	 */
	public function testimonialList(Request $request)
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

			$data = $this->testimonialRepositoryI->testimonialList($inputs);
			$response = $this->sendData($request, $this->success, $data);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}

    /**
     * Function to get testimonial detail
     * @param Request $request, $testimonialId
     * @return Response
     * @author  Rohit Singh
     * @api api/admin/testimonial/edit/{testimonialId}
     */
    public function editTestimonial(Request $request, $testimonialId)
    {
        try {
			$data = $this->testimonialRepositoryI->find([
                "id",
                "name",
                "description",
                \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
            ],
            [
                "id" => $testimonialId
            ]);

            $response = $this->sendData($request, $this->success, ['data' => $data]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }

        return $response;
    }

    /** Function to update testimonial status
     * @param Request $request
     * @return object
     * @author  Rohit Singh
     * @api api/admin/testimonial/status-update
     */
    public function updateTestimonialStatus(Request $request)
    {
        try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:testimonials',
                'status' => 'required|in:1,2'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $updateData = [
                "status" => $inputs['status']
            ];
            $this->testimonialRepositoryI->update($updateData,["id" => $inputs['id']]);
            
            $response = $this->sendJson($request, $this->success,["message"=>"Testimonial status changed successfully."]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
	}

    /**
	 * Function to update web information
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/admin/update-web-information
	 */
	public function updateWebInformation(Request $request)
	{
		try {
            $inputs = $request->all();

            $data = $this->homeRepositoryI->find([
                "id",
                "about_title",
                "about_description",
                "contact_email",
                "contact_phone",
                "about_title",
                "contact_location",
                "terms_description",
                "privacy_description",
                \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
            ],[]);
            
            if(!empty($data)){
                $updateData=[
                    "about_title" => $inputs['about_title'] ?? $data->about_title,
                    "about_description" => $inputs['about_description'] ?? $data->about_description,
                    "contact_email" => $inputs['contact_email'] ?? $data->contact_email,
                    "contact_phone" => $inputs['contact_phone'] ?? $data->contact_phone,
                    "contact_location" => $inputs['contact_location'] ?? $data->contact_location,
                    "terms_description" => $inputs['terms_description'] ?? $data->terms_description,
                    "privacy_description" => $inputs['privacy_description'] ?? $data->privacy_description,
                ];
                if($request->hasFile('about_image')) {
                    $media = uploadFileToLocal($request->file('about_image'),"images");
                    $updateData['about_media_id'] = $media['id'];
                }
                $this->homeRepositoryI->update($updateData,["id" => $data->id ]);
            }
            else{
                $updateData=[
                    "about_title" => $inputs['about_title'] ?? "",
                    "about_description" => $inputs['about_description'] ?? "",
                    "contact_email" => $inputs['contact_email'] ?? "",
                    "contact_phone" => $inputs['contact_phone'] ?? "",
                    "contact_location" => $inputs['contact_location'] ?? "",
                    "terms_description" => $inputs['terms_description'] ?? "",
                    "privacy_description" => $inputs['privacy_description'] ?? "",
                ]; 
                if($request->hasFile('about_image')) {
                    $media = uploadFileToLocal($request->file('about_image'),"images");
                    $updateData['about_media_id'] = $media['id'];
                }
                $this->homeRepositoryI->create($updateData);
            }
			$response = $this->sendData($request, $this->success,[]);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}

    /**
     * Function to get web detail
     * @param Request $request
     * @return Response
     * @author  Rohit Singh
     * @api api/admin/get-web-information
     */
    public function getWebInformation(Request $request)
    {
        try {
            $data = $this->homeRepositoryI->find([
                "id",
                "about_title",
                "about_description",
                "contact_email",
                "contact_phone",
                "about_title",
                "contact_location",
                "terms_description",
                "privacy_description",
                \DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 330 MINUTE),'%d %M %Y %h:%i %p') as date"),
            ],[]);

            $response = $this->sendData($request, $this->success, ['data' => $data]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }

        return $response;
    }
    
}
