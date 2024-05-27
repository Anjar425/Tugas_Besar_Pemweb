<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdministratorController extends Controller
{

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'body'    => $result,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 401)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['body'] = $errorMessages;
        }


        return response()->json($response, $code);
    }


    public function register(Request $request){
        $validator = Validator::make($request -> all(), [
            'name' => 'required',
            'email' => 'required|email|unique:administrators',
            'password' => 'required',
            'c_password' => 'required'
        ]);

        if($validator -> fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request -> all();
        $input['password'] = bcrypt($input['password']);

        $administrator = Administrator::create($input);

        event(new Registered($administrator));

        $success['token'] =  $administrator->createToken('MyApp')->plainTextToken;
        $success['name'] =  $administrator->name;

        return $this->sendResponse($success, 'User register successfully.');    
    }

    /**
     * Handle login for administrators.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('administrators')->attempt($credentials)) {
            return redirect()->intended('dashboard')->withSuccess('Signed in');
        }

        return redirect("administrators")->withErrors('Login details are not valid');
    }

    /**
     * Show the administrator dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function dashboard()
    {
        if (Auth::guard('administrators')->check()) {
            return view('dashboard');
        }

        return redirect("/")->withErrors('You are not allowed to access');
    }

}
