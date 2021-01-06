<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateCustomerAuthToken;
use App\Http\Requests\SaveCustomerStatus;
use App\Http\Requests\StoreCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApiCustomersController extends Controller
{
    public function store(Request $request)
    {
        $rulesArray = [
            'first_name' => 'required',
            'last_name' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required|regex:"^\+?[1-9]\d{1,14}$"|unique:customers|min:11|max:14',
            'gender' => 'required|in:male,female',
            // 'birthdate' => 'required|date_format:"YYYY-MM-DD"|before:today',
            'avatar' => 'required|mimes:jpg,jpeg,png',
            'email' => 'email|unique:customers',
        ];
        $messagesArray = [
            'first_name.required' => 'blank',
            'last_name.required' => 'blank',

            'country_code.required' => 'blank',
            'country_code.regex' => 'inclusion',
            
            'phone_number.required' => 'blank',
            'phone_number.regex' => 'not_a_number',
            'phone_number.unique' => 'taken',
            'phone_number.min' => 'too_short',
            'phone_number.max' => 'too_long',
            
            'gender.required' => 'inclusion',
            'gender.in' => 'invalid',

            'birthdate.required' => 'blank',
            'birthdate.before' => 'in_the_future',

            'avatar.required' => 'blank',
            'avatar.mimes' => 'invalid_content_type',

            'email.unique' => 'taken',
            'email.email' => 'invalid',

        ];

        $validator = Validator::make($request->all(), $rulesArray, $messagesArray);

        if ($validator->fails()) {
            return response()->json(['status' => '400', 'errors' => $validator->errors()]);
        }

        $customer = new Customer();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->country_code = $request->country_code;
        $customer->phone_number = $request->phone_number;
        $customer->gender = $request->gender;
        $customer->birthdate = $request->birthdate;
        $customer->email = $request->email;

        if ($avatar = $request->avatar) {
            $path = 'uploads/' . $customer->id . '/';
            $avatar_new_name = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move($path, $avatar_new_name);
            $customer->avatar = $path . $avatar_new_name;
        }
        $created_customer = $customer->save();
        if ($created_customer) {
            return response()->json(['status' => '201', 'message' => 'The customer created successfully']);
        } else {
            return response()->json(['status' => '500']);
        }
    }

    public function generate_auth_token(Request $request)
    {
        $rulesArray = [
            'phone_number' => 'required',
            'password' => 'required',
        ];
        $messagesArray = [
            'phone_number.required' => 'blank',
            'password.required' => 'blank',
        ];

        $validator = Validator::make($request->all(), $rulesArray, $messagesArray);

        if ($validator->fails()) {
            return response()->json(['status' => '400', 'errors' => $validator->errors()]);
        }

        $customer = Customer::where('phone_number', $request->phone_number)->first();
        if ($customer) {
            $customer->password = Hash::make($request->password);
            $customer->auth_token = Str::random(60);
            $saved_customer = $customer->save();
            if ($saved_customer) {
                return response()->json(['status' => '201', 'message' => 'The customer auth data saved successfully', 'customer' => $customer]);
            } else {
                return response()->json(['status' => '500']);
            }
        } else {
            return response()->json(['status' => '404', 'message' => 'Customer not found']);
        }
        
    }

    public function save_status(Request $request)
    {
        $rulesArray = [
            'phone_number' => 'required',
            'auth_token' => 'required',
            'status' => 'required|in:0,1',
        ];
        $messagesArray = [
            'phone_number.required' => 'blank',
            'auth_token.required' => 'blank',
        ];

        $validator = Validator::make($request->all(), $rulesArray, $messagesArray);

        if ($validator->fails()) {
            return response()->json(['status' => '400', 'errors' => $validator->errors()]);
        }

        $customer = Customer::where('phone_number', $request->phone_number)->where('auth_token', $request->auth_token)->first();

        if ($customer) {
            $customer_status = new CustomerStatus();
            $customer_status->customer_id = $customer->id;
            $customer_status->phone_number = $customer->phone_number;
            $customer_status->auth_token = $customer->auth_token;
            $customer_status->status = $request->status;
            $created_customer_status = $customer_status->save();
            if ($created_customer_status) {
                return response()->json(['status' => '201', 'message' => 'The customer status saved successfully']);
            } else {
                return response()->json(['status' => '500']);
            }
        } else {
            return response()->json(['status' => '404', 'message' => 'Customer not found']);
        }
    }}
