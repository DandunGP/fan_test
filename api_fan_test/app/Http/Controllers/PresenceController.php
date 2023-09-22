<?php

namespace App\Http\Controllers;

use App\Models\Epresence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    public function get_presense($id){
        $dataPresence = Epresence::where('id', $id)->first();

        if($dataPresence){
            return response_json(200, 'success', $dataPresence);
        }

        return response_json(404, 'error', 'data not found!');
    }

    public function get_all_presence(){
        $dataPresence = Epresence::all();
        $datePresence = array();
        $userIdPresence = array();
        $result = array();

        if($dataPresence->count() != 0){
            foreach ($dataPresence as $dp){
                $date = explode(' ', $dp->waktu);
                if(!in_array($date[0], $datePresence)){
                    array_push($datePresence, $date[0]);
                }
            }
            
            foreach ($datePresence as $dps){
                $data = Epresence::whereDate("waktu", $dps)->get();
                foreach($data as $d){
                    if(!in_array($d->user_id, $userIdPresence)){
                        array_push($userIdPresence, $d->user_id);
                    }
                }
            }

            foreach($datePresence as $dps){
                foreach($userIdPresence as $usp){
                    $data = Epresence::whereDate("waktu", $dps)->where('user_id', $usp)->where('type', 'IN')->first();
                    $dataOut = Epresence::whereDate("waktu", $dps)->where('user_id', $data->user_id)->where('type', 'OUT')->first();
        
                    if($data && $dataOut){
                        $waktu_masuk = explode(' ', $data->waktu);
                        $waktu_keluar = explode(' ', $dataOut->waktu);

                        $status_masuk = "REJECT";
                        $status_keluar = "REJECT";
                        
                        if($data->is_approve == true){
                            $status_masuk = "APPROVE";
                        }

                        if($dataOut->is_approve == true){
                            $status_keluar = "APPROVE";
                        }

                        $dataPresenceUserResponse = [
                            "id_user" => $data->user_id,
                            "nama_user" => $data->user->name,
                            "tanggal" => $waktu_masuk[0],
                            "waktu_masuk" => $waktu_masuk[1],
                            "waktu_keluar" =>  $waktu_keluar[1],
                            "status_masuk" => $status_masuk,
                            "status_keluar" => $status_keluar
                        ];

                        array_push($result, $dataPresenceUserResponse);
                    }
                }
            }
            return response_json(200, 'success get data', $result);
        }

        return response_json(404, 'error', 'data not found!');
    }

    public function create_presence(Request $request){
        Validator::extend('time_format', function ($attribute, $value, $parameters, $validator) {
            $format = 'Y-m-d H:i:s';
        
            $parsedDate = \DateTime::createFromFormat($format, $value);
            
            return $parsedDate !== false && $parsedDate->format($format) === $value;
        });

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'waktu' => 'required|time_format',
        ], [
            'waktu.time_format' => 'The time format entered is incorrect, example : Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response_json(422 , 'failed', $validator->messages());
        }

        $userId = get_id_user_jwt($request);

        $hariIni = Carbon::now()->format("Y-m-d");

        $checkTwoPresence = Epresence::whereDate('waktu',  $hariIni)->where('user_id', $userId)->count();

        if($checkTwoPresence == 2){
            return response_json(409 , 'failed', 'you already presence IN and OUT');
        }

        $checkPresence = Epresence::where('user_id', $userId)->whereDate('waktu',  $hariIni)->orderBy('id', 'DESC')->first();

        if($checkPresence){
            if ($checkPresence->type != $request->type){
                Epresence::insert([
                    "user_id" => $userId,
                    "type" => $request->type,
                    "waktu" => $request->waktu,
                    "is_approve" => false
                ]);
    
                return response_json(200 , 'success presence OUT', '');
            }

            return response_json(409 , 'you already presence IN', '');
        }

        if($request->type != "OUT"){
            Epresence::insert([
                "user_id" => $userId,
                "type" => $request->type,
                "waktu" => $request->waktu,
                "is_approve" => false
            ]);

            return response_json(200 , 'success presence IN', '');
        }

        return response_json(409 , 'failed', 'you have not performed an IN presence');
    }
    
    public function approve_presence(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response_json(422 , 'failed', $validator->messages());
        }

        $preseceData = Epresence::where('id', $request->id)->first();

        if($preseceData){
            //get data user presence
            $userData = User::where('id', $preseceData->user_id)->first();

            //get data supervisor login
            $supervisorID = get_id_user_jwt($request);
            $supervisorData = User::where('id', $supervisorID)->first();

            //check NPP Supervisor
            if($userData->npp_supervisor == $supervisorData->npp){
                $dataPresence = Epresence::where('id', $request->id)->first();
                $dataPresence->timestamps = false;

                if($dataPresence->is_approve != true){
                    $dataPresence->update([
                        'is_approve' => true
                    ]);

                    return response_json(200 , 'failed', 'success approve presence');
                }

                return response_json(409 , 'failed', 'youre already approved');
            }

            return response_json(409 , "failed", "You are not this user's supervisor");
        }

        return response_json(404 , 'error', 'presence not found!');
    }
}
