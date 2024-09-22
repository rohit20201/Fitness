<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Core\RequestController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use Carbon\Carbon;
use Log;

class HomeController extends RequestController
{
   
    /**
	 * Function to save contact
	 * @param Request $request
	 * @return Response
	 * @author Rohit Singh
	 * @api api/save-contact
	 */
	public function saveContact(Request $request)
	{
		try {
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'email',
                'phone' => 'required|string|max:15',
            ]);
    
            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $data=[
                "name" => $inputs['name'],
                "city" => $inputs['city'] ?? "",
                "email" => $inputs['email'] ?? "",
                "phone" => $inputs['phone'],
                "message" => $inputs['message'] ?? ""
            ];

            $details = [
                'title' => 'Contact Details',
                'body' => 'You have received new contact details',
                'data' => $data
            ];
    
            Mail::to('rohit.fitness@gmail.com')->send(new ContactEmail($details));
            
            insertData(Contact::class,[
                "data" => $data,
            ]);

			$response = $this->sendData($request, $this->success,[]);
		} catch (\Exception $e) {
			report($e);
			$response = $this->sendError($request);
		}
		return $response;
	}
}
