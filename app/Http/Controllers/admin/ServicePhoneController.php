<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ServicePhone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServicePhoneController extends Controller
{
    public function index(Request $request){
        
        return view('admin.service_phone.index', compact(''));
    }

    public function api_index(Request $request){

        $data = ServicePhone::all();

        $service_phones = ['code' => 0,"msg"=>"获取数据成功","count"=>$data->count(),'data'=>$data];
        
        return response()->json($service_phones);
    }

    public function create(){
        return view('admin.service_phone.create');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:service_phones|max:20',
            'name' => 'required',
            'area_code' => 'required',
        ])->validate();

        $req = $request->only(['name','phone','area_code']);

        $res = ServicePhone::create($req);

        if($res){
            return response()->json(['err' => '1', 'msg' => '添加成功!']);
        }else{
            return response()->json(['err' => '0', 'msg' => '添加失败!']);
        }
        
    }

    public function edit(Request $request, $id){

        $detail = ServicePhone::find($id);
        return view('admin.service_phone.edit', compact('detail'));
    }

    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                Rule::unique('service_phones')->ignore($id),
            ],
            'name' => 'required',
            'area_code' => 'required',
        ])->validate();

        $req = $request->only(['name', 'phone', 'area_code', 'round']);

        $status = $request->post('status');

        $req['disabled_at'] = $status ? null : Carbon::now();

        $res = ServicePhone::where('id', $id)->update($req);

        if($res){
            return response()->json(['err' => '1', 'msg' => '成功!']);
        }else{
            return response()->json(['err' => '0', 'msg' => '失败!']);
        }

    }

    public function destroy(Request $request, $id){
        $sp = ServicePhone::find($id);

        if($sp){
            $res = $sp->delete();

            if($res){
                return response()->json(['err' => '1', 'msg' => '成功!']);
            }else{
                return response()->json(['err' => '0', 'msg' => '失败!']);
            }
        }else{
            return response()->json(['err' => '0', 'msg' => '数据不存在!']);
        }
    }
}
