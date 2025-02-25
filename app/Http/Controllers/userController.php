<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class userController extends Controller {
    
    public function saveAddUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'userEmail'=>'required|email',
            'userFirstName'=>'required',
            'userLastName'=>'required',
            'userContact'=>'required|integer|digits:10|between:1000000000,9999999999',
            // 'userPassword'=>'required|min:8',
            'userPassword'=>['required','min:8',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ],[
            'userEmail.required'=>'User email is required',
            'userEmail.email'=>'User email should be valid email address',

            'userFirstName.required'=>'User first name is required',
            'userLastName.required'=>'User last name is required',
            
            'userContact.required'=>'User contact is required',
            'userContact.integer'=>'User contact must be in numbers',
            'userContact.digits'=>'User contact must be of 10 digits',
            'userContact.between'=>'User contact number should not start with 0',
            
            'userPassword.required'=>'User password is required',
            'userPassword.min'=>'User password must be atleast :min characters',
        ]);

        if($validator->fails()){
            // return dd($validator->messages());
            return redirect()->back()->with('validationMsg', $validator->messages())->withInput();
        }

        // checking in db if same email found
        $sameemail = DB::table('customers')
                        ->where('email', '=', $request->userEmail)
                        ->get();
        if(count($sameemail) > 0)
            return redirect()->back()->with('error','User with same email already exists');

        $salt = strval(rand(100, 999));
        $pepper = "COMPANY";
        $request->userPassword = $pepper.$request->userPassword.$salt;
        $customersdb = DB::table('customers')
                        ->insert([
                            'email'=>$request->userEmail,
                            'full_name'=>$request->userFirstName."-".$request->userMiddleName."-".$request->userLastName,
                            'password'=>$request->userPassword,
                            'salt'=>$salt,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
        return redirect()->back()->with('success','User registered successfully 😀');
    }

    public function verifyUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'userEmail'=>'required|email',
            // 'userPassword'=>'required|min:8',
            'userPassword'=>['required','min:8',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ],[
            'userEmail.required'=>'User email is required',
            'userEmail.email'=>'User email should be valid email address',

            'userPassword.required'=>'User password is required',
            'userPassword.min'=>'User password must be atleast :min characters',
        ]);

        if($validator->fails()){
            // return dd($validator->errors());
            return redirect()->back()->withInput()->with('validationMsg', $validator->messages());
        }
        
        $customersdb = DB::table('customers')
                        ->where('email', '=', $request->userEmail)
                        ->get();
        // email found in db
        if(count($customersdb) > 0){
            $salt = $customersdb[0]->salt;
            $pepper = "COMPANY";
            $request->userPassword = $pepper.$request->userPassword.$salt;
            // password don't match in db
            if($customersdb[0]->password != $request->userPassword)
                return redirect()->back()->withInput()->with('error', 'Invalid Password');

            // successfull LOGIN
            $name = $customersdb[0]->full_name;
            $name = explode("-", $name);
            $cust_name = "";
            foreach ($name as $index => &$word) {
                if($word != '') {
                    if($index == 0)
                        $cust_name = $word;
                    else
                        $cust_name = $cust_name." ".$word;
                }                
            }
            // save the Customer INFO in session storage
            session()->put('id',$customersdb[0]->id);
            session()->put('cust_name',$cust_name);
            session()->put('cust_email',$customersdb[0]->email);
            return redirect()->route('dashboardUser');
        }
        // nothing founds in db
        else{
            // session()->flash('error', 'No records found');
            return redirect()->back()->with('error', 'No records found with this email');
        }
    }

    public function dashboardUser() {
        $customerdetails = DB::table('customer_details')
                            ->where('cust_id', '=', session('id'))
                            ->orderBy('created_at', 'desc')
                            ->get();
        // session()->flash('details', $customerdetails);
        // return view('User/customer-dashboard');
        return view('User/customer-dashboard')->with('details', $customerdetails);
    }

    public function addAppointment(Request $request) {
        // return dd($request->all());
        // store into session memory till TIMINGS are get
        session()->put('appointmentType', $request->all());
        return redirect()->route('againAddAppointment');
    }

    public function saveAppointment(Request $request) {
        $validator = Validator::make($request->all(), [
            'timeShift'=>'required',
            'timeDuration'=>'required',
        ],[
            'timeShift.required'=>'Time shift is required',
            'timeDuration.required'=>'Time duration is required',
        ]);

        if($validator->fails()){
            // return dd($validator->errors());
            // session()->flash('validationMsg', $validator->messages());
            return redirect()->route('againAddAppointment')->withInput()->with('validationMsg', $validator->messages());
        }

        $appointmentType = session()->get('appointmentType');
        session()->forget('appointmentType');
        $services = "";
        foreach($appointmentType['selected_services'] as $index => $value){
            if($index == 0)
                $services = $value;
            else    
                $services = $services.", ".$value;
        }
        $customerdb = DB::table('customer_details')
                        ->insert([
                            'cust_id'=>session('id'),
                            'services'=>$services,
                            'cost'=>$appointmentType['total_cost'],
                            'time_shift'=>$request->timeShift,
                            'time_duration'=>$request->timeDuration,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
        $customerdetails = DB::table('customer_details')
                            ->where('cust_id', '=', session('id'))
                            ->orderBy('created_at', 'desc')
                            ->get();
        return redirect()->route('dashboardUser')->with('details', $customerdetails);
    }

    public function viewAppointment($id) {
        $details = DB::table('customer_details')
                    ->where('id', '=', $id)
                    ->get();
        $servicesdb = $details[0]->services;
        $services = explode(",", $servicesdb);
        foreach ($services as &$service) {
            $service = ltrim($service);
            $service = rtrim($service);
        }
        return view('User/view-appointment')->with([
            'details'=>$details[0],
            'services'=>$services,
        ]);
    }

    public function editAppointment($id) {
        $details = DB::table('customer_details')
                    ->where('id', '=', $id)
                    ->get();
        $servicesdb = $details[0]->services;
        $services = explode(",", $servicesdb);
        foreach ($services as &$service) {
            $service = ltrim($service);
            $service = rtrim($service);
        }
        
        return view('User/edit-appointment')->with([
            'details'=>$details[0],
            'services'=>$services,
        ]);
    }

    public function updateAppointment(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'timeShift'=>'required',
            'timeDuration'=>'required',
        ],[
            'timeShift.required'=>'Time shift is required',
            'timeDuration.required'=>'Time duration is required',
        ]);

        if($validator->fails()){
            // return dd($validator->errors());
            // session()->flash('validationMsg', $validator->messages());
            return redirect()->back()->withInput()->with('validationMsg', $validator->messages());
        }

        $service_name = $request->selected_services;
        $services = "";
        // SERVICE Type & TIMING both are changed
        if(isset($service_name)) {
            foreach($service_name as $index => $value){
                if($index == 0)
                    $services = $value;
                else    
                    $services = $services.", ".$value;
            }
            $customerdb = DB::table('customer_details')
                            ->where('id', '=', $id)
                            ->update([
                                'services'=>$services,
                                'cost'=>$request->total_cost,
                                'time_shift'=>$request->timeShift,
                                'time_duration'=>$request->timeDuration,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
        }
        // only TIMING are changed
        else {
            $customerdb = DB::table('customer_details')
                            ->where('id', '=', $id)
                            ->update([
                                'time_shift'=>$request->timeShift,
                                'time_duration'=>$request->timeDuration,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
        }
        // goto Dashboard
        return redirect()->route('dashboardUser');
    }

    public function deleteAppointment($id) {
        $customerdb = DB::table('customer_details')
                        ->where('id', '=', $id)
                        ->delete();
        return redirect()->route('dashboardUser');
    }

    public function userLogout() {
        // delete all session storage data
        session()->flush();
        // session()->flash('success','You logged out successfully 😀');
        return redirect('/')->with('success','You logged out successfully 😀');
    }
}
