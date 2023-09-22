<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get_user($id){
        $dataUser = User::where('id', $id)->first();

        if($dataUser){
            return response_json(200, 'success', $dataUser);
        }

        return response_json(404, 'error', 'data not found!');
    }

    public function get_all_user(){
        $dataUser = User::all();

        if($dataUser->count() != 0){
            return response_json(200, 'success', $dataUser);
        }

        return response_json(404, 'error', 'data not found!');
    }

    public function create_user(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'npp' => 'required',
            'npp_supervisor' => '',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response_json(422 , 'failed', $validator->messages());
        }
        
        if($request->npp_supervisor){
            $checkUser = User::where('npp', $request->npp_supervisor)->first();
            if($checkUser){
                User::insert([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'npp' => $request->npp,
                    'npp_supervisor' => $request->npp_supervisor,
                    'password' => Hash::make($request->password),
                ]);

                return response_json(200, 'success add new user', "");
            }

            return response_json(404, 'error', 'npp supervisor not found!');
        }
    }
}
