<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateRecord;
use App\Models\User;
use App\Models\UserRecord;
use Illuminate\Support\Facades\Validator;

class UserRecordsController extends Controller
{
    //list records
    public function index()
    {
        $records = UserRecord::select('name','email','experience','avatar')->get();
        if ($records->count() > 0) {
            $data = [
                'status' => 200,
                'response' => $records
            ];
        } else {
            $data = [
                'status' => 499,
                'response' => 'No records found'
            ];
        }
        return response()->json($data, $data['status']);
    }

    //create new record
    public function store(Request $r)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:user_records',
            'experience' => 'required',
            'avatar' => 'sometimes|file'
        ];
        $v = Validator::make($r->all(), $rules);
        if ($v->fails()) {
            $error = $v->errors()->first();
            $data = [
                'status' => 422,
                'response' => $error
            ];
        } else {
            $inputs = $v->validated();
            $record = UserRecord::create($inputs);
            if ($r->hasFile('avatar')) {
                $path = $r->file('avatar')->store('public');
                $path = explode("/", $path)[1];

                $record->update(['avatar' => $path]);
            }

            $data = ['status' => 201, 'response' => 'record created'];
        }
        return response()->json($data, $data['status']);
    }

    //delete record
    public function delete($id)
    {
        UserRecord::destroy($id);
        $data = [
            'status' => 204,
            'response' => "user record deleted"
        ];
        return response()->json($data, $data['status']);
    }
}
