<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{

    protected $unauthorized = 401;
    protected $notFound = 404;
    protected $badRequest = 400;
    protected $methodNotAllowed = 405;
    protected $success = 200;
    protected $forbidden = 403;
    protected $requestError = 422;
    protected $serverError = 500;

    protected $maintenance = 503;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected function getData(Request $request) : array
    {
        return $request->all();
    }

    protected function sendData(Request $request, $status, $data = [])
    {

        $response = (object) $data;  //["data" => (object) $data];

        $json = json_encode($response);

        if($request->odnr) // only data no response
        {
            return $data;
        }

        return response($json, $status);

    }

    protected function sendError(Request $request, $status =  "", $message = "", $code = 0)
    {


        if(empty($status))
        {
            $status =  $this->serverError;
            $message = "Sorry! Something went wrong.";
            $code = 500;
        }
		if(isset($message['data']))
		{
			$response = ["error" => (object) ["message" => $message['message'],"data" => $message['data'], "code" => $code]];
		}
		else
		{
			$response = ["error" => (object) ["message" => $message, "code" => $code]];
		}

        $json = json_encode($response);

        return response($json,$status);

    }

    protected function sendValidationError(Request $request, $validate)
    {


        $status =  $this->requestError;
        $message = "Validations are missing.";

        $response = ["error" => (object) ["message" => $message, "validate" => $validate]];

        return response($response,$status);

    }

    /**
     * Function to return response in json format with status
     * @param Request $request
     * @param int $status
     * @param $data
     * @return JsonResponse
     */

    protected function sendJson(Request $request,int $status,$data = []) : JsonResponse
    {
        return response()->json((object)$data,$status);
    }

    /**
     * Function to return validation error response in json format
     * @param Request $request
     * @param $validate
     * @return JsonResponse
     */

    protected function sendValidationErrorJson(Request $request, $validate) : JsonResponse
    {

        $status =  $this->requestError;

        $message = "Validations are missing.";

        $response = ["error" => (object) ["message" => $message, "validate" => $validate]];

        return $this->sendJson($request,$status,$response);

    }

    /**
     * Function to return error response in json format
     * @param Request $request
     * @param int $status
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */

    protected function sendJsonError(Request $request,int $status = 0,$message = "",int $code = 0) : JsonResponse
    {

        if(empty($status))
        {
            $status =  $this->serverError;
            $message = "Sorry! Something went wrong.";
            $code = 500;
        }

        if(isset($message['data']))
        {
            $response = ["error" => (object) ["message" => $message['message'],"data" => $message['data'], "code" => $code]];
        }
        else
        {
            $response = ["error" => (object) ["message" => $message, "code" => $code]];
        }

        return $this->sendJson($request,$status,$response);

    }

}


