<?php

namespace App\Http\Controllers\admin;

use App\admin;
use App\channel\skuSDK;
use App\config_val;
use App\goods;
use App\goods_config;
use App\goods_kind;
use App\kind_config;
use App\kind_sku;
use App\kind_val;
use App\order;
use App\product_type;
use App\special;
use App\spend;
use App\supplier;
use App\url;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\vis;
use DB;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KindController extends Controller
{
    /** 列表页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $counts = DB::table('goods_kind')
            ->count();
        return view('admin.kind.index')->with('counts', $counts);
    }

    /** 产品列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_table(Request $request)
    {
        $page = $request->input('page',1);
        $limit = $request->input('limit',10);
        $search = $request->input('search');
        //排序参数
        $field = $request->input('field','goods_kind_id'); //排序字段
        $dsc = $request->input('order','desc'); //排序顺序
        $start = ($page-1)*$limit;

        //产品个数
        $newcount = goods_kind::join('product_type', 'product_type_id', '=', 'goods_product_id','left')
        ->where(function ($query) use ($search,$request) {
            if($request->has('search') && trim($request->input('search'))){
                $query->where('goods_kind_name', 'like', "%$search%");
                $query->orWhere('goods_kind_sku', 'like', "$search%");
                $query->orWhere('goods_kind_english_name', 'like', "%$search%");
            }
        })
        ->where(function ($query) {
            $query->whereIn('goods_kind_admin', \App\admin::get_admins_id());
        })
        ->where(function ($query) use ($request) {
            if ($request->input('product_type_id') != 0) {
                $query->where('goods_product_id', $request->input('product_type_id'));
            }
        })
        ->where(function($query)use($request){ //时间筛选
            if($request->input('min')&&$request->input('max')){
                $query->whereBetween('goods_kind.goods_kind_time',[$request->input('min').' 00:00:00',$request->input('max').' 00:00:00']);
            }
        })
        ->count();

        //产品信息
        $data = DB::table('goods_kind')->join('product_type', 'product_type_id', '=', 'goods_product_id','left')
            ->where(function ($query) use ($search,$request) {
                //根据用户获取用户查看权限
                $query->whereIn('goods_kind_admin', \App\admin::get_admins_id());

                //关键字搜索
                if($request->has('search') && trim($request->input('search'))){
                    $query->where('goods_kind_name', 'like', "%$search%");
                    $query->orWhere('goods_kind_sku', 'like', "$search%");
                    $query->orWhere('goods_kind_english_name', 'like', "%$search%");
                }

                //根据产品分类搜索
                if ($request->has('product_type_id') && $request->input('product_type_id') != 0) {
                    $query->where('goods_product_id', $request->input('product_type_id'));
                }

                //时间筛选
                if($request->input('min')&&$request->input('max')){
                    $query->whereBetween('goods_kind.goods_kind_time',[$request->input('min').' 00:00:00',$request->input('max').' 00:00:00']);
                }
        })
        ->select('goods_kind.*','product_type.product_type_name')
        ->orderBy($field, $dsc)
        ->offset($start)
        ->limit($limit)
        ->get();
        if (!$data->isEmpty()) {
            foreach ($data as &$item) {
                $item->num = goods::where('goods_kind_id', $item->goods_kind_id)->where('is_del', '0')->count();
            }
        }
//        $arr = ['draw' => $page, 'recordsTotal' => $counts, 'recordsFiltered' => $newcount, 'data' => $data];
        $arr = ['code' => 0,"msg"=>"获取数据成功","count"=>$newcount,'data'=>$data];
        return response()->json($arr);
    }

    /** 新增产品
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function addkind(Request $request)
    {
        //新增产品
        if ($request->isMethod('get')) {
            $id = $request->input('id') ? $request->input('id') : 0;
            $goods_kinds = \App\goods_kind::get();
            foreach ($goods_kinds as $k => $v) {
                $goods_kinds[$k]->goods_kind_name = $v->goods_kind_name . '(' . goods::where('goods_kind_id', $v->goods_kind_id)->count() . ')';
            }
            return view('admin.kind.addkind')->with(compact('goods_kinds', 'id'));
        } elseif ($request->isMethod('post')) {
            //新增产品属性名、属性值
            //1.验证字段是否漏填
            $goods_config_name = $request->input('goods_config_name');
            $data_null = false; //判断产品是否只有一个属性，并且为空，属性为空为true；
            $goods_config_color = [];
            $goods_color_sku = [];
            if($goods_config_name && count($goods_config_name) > 3) {
                return response()->json(['err' => '0', 'msg' => '产品属性不能超过三组!']);
            }
            if ($goods_config_name) {
                foreach ($goods_config_name as $key=>$item) {
                    if ($data_null == false) {
                        if (!isset($item['goods_config_name']) || !$item['goods_config_name'] || !isset($item['msg']) || empty($item['msg']) || !isset($item['goods_config_english_name']) || !$item['goods_config_english_name']) {
                            return response()->json(['err' => '0', 'msg' => '产品属性名，或产品英文属性名，属性值，属性英文值均不能为空!']);
                        }
                        foreach ($item['msg'] as $val) {
                            if(in_array($item['goods_config_name'],['颜色','顏色']) || strtolower($item['goods_config_english_name'] == 'color' || strtolower($item['goods_config_name']) == 'color')){
                                if (!$val['goods_config'] || !$val['goods_config_english'] || !$val['color']) {
                                    return response()->json(['err' => '0', 'msg' => '产品属性值、产品英文属性值、色系不能为空!']);
                                }
                            }else{
                                if (!$val['goods_config'] || !$val['goods_config_english']) {
                                    return response()->json(['err' => '0', 'msg' => '产品属性值、产品英文属性值不能为空!']);
                                }
                            }
                            if(isset($val['color']) && $val['color']){
                                if(in_array($val['color'],$goods_config_color)){
                                    $goods_color_sku[$val['color']]++;
                                }else{
                                    $goods_color_sku[$val['color']] = 0;
                                    array_push($goods_config_color,$val['color']);
                                }
                            }
                            if (!isset($val['goods_config']) || !$val['goods_config'] || !isset($val['goods_config_english']) || !$val['goods_config_english']) {
                                return response()->json(['err' => '0', 'msg' => '产品属性值、产品英文属性值、色系不能为空!']);
                            }

                        }
                        if ((count($item['msg']) != count(array_unique(array_column($item['msg'], 'goods_config')))) || (count($item['msg']) != count(array_unique(array_column($item['msg'], 'goods_config_english'))))){
                            return response()->json(['err' => '0', 'msg' => '产品属性值或产品英文属性值不能重复!']);
                        }
                    }
                }
            }
            if (!$request->has('goods_kind_name') || $request->input('goods_kind_name') == '' || $request->input('goods_kind_name') == null) {
                return response()->json(['err' => '0', 'msg' => '信息错误!']);
            }

            //2.验证产品名是否重复
            $goods_kind_name = $request->input('goods_kind_name');
            $goods_kind = goods_kind::where('goods_kind_name', $goods_kind_name)->first();
            if ($goods_kind) {
                return response()->json(['err' => '0', 'msg' => '产品名称已存在，添加失败!']);
            }

            //3.新增产品
            $goods_kind = new \App\goods_kind;
            $goods_kind->goods_kind_name = $goods_kind_name;
            $goods_kind->goods_kind_english_name = $request->input('goods_kind_english_name') ? $request->input('goods_kind_english_name') : '';
            $goods_kind->goods_kind_yn_name = $request->input('goods_kind_yn_name') ? $request->input('goods_kind_yn_name') : '';
            $goods_kind->goods_kind_volume = $request->input('width', 0) . 'cm*' . $request->input('depth', 0) . 'cm*' . $request->input('height', 0) . 'cm';
            $goods_kind->goods_kind_postage = $request->input('goods_kind_postage', 0) == null ? 0 : $request->input('goods_kind_postage', 0);
            $goods_kind->goods_kind_user_type = $request->input('goods_kind_user_type', 0) == null ? 0 : $request->input('goods_kind_user_type', 0);
            $img = $request->file('goods_kind_img');
            $img_name = '';
            if ($img) {
                $size = filesize($img);
                if ($size > 8 * 1024 * 1024) {
                    return response()->json(['err' => 0, 'str' => '赠品图片不能超过8M！']);
                }
                $file_name = $img->getClientOriginalName();
                $ext = $img->getClientOriginalExtension();
                $fileName = md5(uniqid($file_name));
                $newImagesName = 'fm' . "_" . $fileName . '.' . $ext;//生成新的的文件名
                $filedir = "upload/goods_kind/" . date('Ymd') . '/';
                $img->move($filedir, $newImagesName);
                $img_name = $filedir . $newImagesName;
                $goods_kind->goods_kind_img = $img_name;
            }
//            $goods_kind->goods_buy_url = $request->input('goods_buy_url');
//            $goods_kind->goods_buy_msg = $request->input('goods_buy_msg');
            $goods_kind->goods_buy_weight = $request->input('goods_buy_weight', 0) == null ? 0 : $request->input('goods_buy_weight', 0);
            $goods_kind->goods_kind_admin = Auth::user()->admin_id;
            $goods_kind->goods_kind_time = date("Y-m-d H:i:s", time());
            $goods_kind->goods_product_id = $request->input('product_type_id');
            $msg = $goods_kind->save();
            
            $kind_primary_id = $goods_kind->goods_kind_id;
            if ($msg) {
                if ($request->input('supplier_url') || $request->input('supplier_tel') || $request->input('supplier_contact') || $request->input('supplier_price') || $request->input('supplier_num') || $request->input('supplier_remark')) {
                    $supplier = new supplier();
                    $supplier->supplier_url = $request->input('supplier_url', '');
                    $supplier->supplier_tel = $request->input('supplier_tel', '');
                    $supplier->supplier_contact = $request->input('supplier_contact', '');
                    $supplier->supplier_price = $request->input('supplier_price') == null ? 0:  $request->input('supplier_price', 0);
                    $supplier->supplier_num = $request->input('supplier_num', 0) == null ? 0 : $request->input('supplier_num', 0);
                    $supplier->supplier_remark = $request->input('supplier_remark', '');
                    $supplier->is_spots = $request->input('is_spots');
                    $supplier->is_spare = 0;
                    $supplier->goods_kind_primary_id = $kind_primary_id;
                    $supplier->save();
                }
                if ($request->input('spare_supplier_url') || $request->input('spare_supplier_tel') || $request->input('spare_supplier_contact') || $request->input('spare_supplier_price') || $request->input('spare_supplier_num') || $request->input('spare_supplier_remark')) {
                    $spare_supplier = new supplier();
                    $spare_supplier->supplier_url = $request->input('spare_supplier_url', '');
                    $spare_supplier->supplier_tel = $request->input('spare_supplier_tel', '');
                    $spare_supplier->supplier_contact = $request->input('spare_supplier_contact', '');
                    $spare_supplier->supplier_price = $request->input('spare_supplier_price') == null ? 0:  $request->input('spare_supplier_price', 0);
                    $spare_supplier->supplier_num = $request->input('spare_supplier_num', 0) == null ? 0 : $request->input('spare_supplier_num', 0);
                    $spare_supplier->supplier_remark = $request->input('spare_supplier_remark', '');
                    $spare_supplier->is_spots = $request->input('spare_supplier_is_spots');
                    $spare_supplier->is_spare = 1;
                    $spare_supplier->goods_kind_primary_id = $kind_primary_id;
                    $spare_supplier->save();
                }
            }
            if ($msg && !$data_null && $goods_config_name) {
                //添加产品属性和产品属性值
                foreach ($goods_config_name as $item) {
                    $kind_config = new kind_config();
                    $kind_config->kind_config_msg = $item['goods_config_name'];
                    $kind_config->kind_config_english_msg = $item['goods_config_english_name'] ? $item['goods_config_english_name'] : '';
                    $kind_config->kind_primary_id = $kind_primary_id;
                    $kind_config_bool = $kind_config->save();
                    $kind_config_id = $kind_config->kind_config_id;
                        //新增属性值
                    if ($kind_config_bool) {
                        foreach ($item['msg'] as $value) {
                            $kind_val = new kind_val();
                            if(in_array($item['goods_config_name'],['颜色','顏色']) || strtolower($item['goods_config_english_name'] == 'color' || strtolower($item['goods_config_name']) == 'color')) {
                                $kind_val->kind_val_sku = isset($value['color']) ? skuSDK::get_color_sku($value['color'],$goods_color_sku) : '00';
                            }
                            $kind_val->kind_val_msg = $value['goods_config'];
                            $kind_val->kind_val_english_msg = $value['goods_config_english'] ? $value['goods_config_english'] : '';
                            $kind_val->kind_primary_id = $kind_primary_id;
                            $kind_val->kind_type_id = $kind_config_id;
                            $kind_val->save();
                        }
                    }

                }
            }
            //生成产品SKU
            $sku_sdk=new \App\channel\skuSDK($goods_kind->goods_kind_id,$request->input('product_type_id'),$request->input('goods_kind_user_type'));
            try{
                $mark=$sku_sdk->set_sku();
                if(!$mark) {
                    $goods_kind->delete();
                    return response()->json(['err' => '0', 'msg' => $sku_sdk->get_error()]);
                }
            }catch(\Exception $e){
                 return response()->json(['err' => '0', 'msg' => 'SKU生成失败！']);
            }
            if ($msg) {
                $ip = $request->getClientIp();
                //加log日志
                operation_log($ip, '新增产品成功,产品名称：' . $goods_kind_name);
                return response()->json(['err' => '1', 'msg' => '添加成功!']);
            } else {
                return response()->json(['err' => '0', 'msg' => '添加失败!']);
            }
        }
    }

    /**
     * 修改产品库存
     * @param Request $request
     */
    public function sku_num(Request $request)
    {
        if($request->isMethod('get')){
            $id = $request->input('id');
            $goods_kind = \App\goods_kind::where('goods_kind_id',$id)->first();
            $goods_kind->attrs = DB::table('kind_config as kc')->join('kind_val as kv','kc.kind_config_id','kv.kind_type_id','join')
                ->where('kc.kind_primary_id',$goods_kind->goods_kind_id)->orderBy('kv.kind_type_id')->get();
            //产品属性信息
            $product_attr = goods_kind::attr_sku_product($goods_kind->goods_kind_id);
            return view('admin.kind.sku_num',compact('goods_kind','product_attr'));
        }else{
            $input = $request->except('_token');

            foreach($input['product'] as $val){
                $where = ['kind_sku' => $val['kind_sku']];
                $result = kind_sku::updateOrInsert($where,$val);
            }

            if($result){
                return response()->json(['err' => 1, 'str' => '保存成功！']);
            }else{
                return response()->json(['err' => 0, 'str' => '保存失败！']);
            }

        }
    }



    /** 修改产品页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function upgoods_kind(Request $request)
    {
        $goods_kinds_id = $request->input('id');
        $goods_kinds = goods_kind::where('goods_kind_id', $goods_kinds_id)->first(['goods_kind_name', 'goods_product_id']);
//        $goods_kinds->supplier = supplier::where('goods_kind_primary_id', $goods_kinds_id)->where('is_spare', 0)->first();
//        dd($goods_kinds->goods_kind_volume);
//        if ($goods_kinds->goods_kind_volume) {
//            $volume = explode('*', $goods_kinds->goods_kind_volume);
//            $goods_kinds->width = str_replace('cm', '', $volume[0]);
//            $goods_kinds->depth = str_replace('cm', '', $volume[1]);
//            $goods_kinds->height = str_replace('cm', '', $volume[2]);
//        }
//        $goods_kinds->spare_supplier = supplier::where('goods_kind_primary_id', $goods_kinds_id)->where('is_spare', 1)->first();
        return view('admin.kind.upgoods_kind')->with(compact('goods_kinds_id', 'goods_kinds'));
    }

    /** 修改产品属性与规格
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function post_update(Request $request)
    {
        if ($request->isMethod('get')) {
            $goods_kinds_id = $request->input('id');
            $goods_kinds = goods_kind::where('goods_kind_id', $goods_kinds_id)->first();
//            if(!$goods_kinds){
//                return view('admin.kind.kind_config_val')->with(compact('goods_kinds','goods_config'));
//            }
            $goods_kinds->supplier = supplier::where('goods_kind_primary_id', $goods_kinds_id)->where('is_spare', 0)->first();
            if ($goods_kinds->goods_kind_volume) {
                $volume = explode('*', $goods_kinds->goods_kind_volume);
                $goods_kinds->width = str_replace('cm', '', $volume[0]);
                $goods_kinds->depth = str_replace('cm', '', $volume[1]);
                $goods_kinds->height = str_replace('cm', '', $volume[2]);
            }

            $goods_kinds->spare_supplier = supplier::where('goods_kind_primary_id', $goods_kinds_id)->where('is_spare', 1)->first();
            $goods_config = \App\kind_config::where('kind_primary_id', $goods_kinds_id)->get();
            if ($goods_config != null) {
                foreach ($goods_config as $k => $v) {
                    $arr = \App\kind_val::where('kind_type_id', $v->kind_config_id)->orderBy('kind_val_id', 'asc')->get()->toArray();
                    if($v->kind_config_msg == '颜色' || $v->kind_config_msg == '顏色'){
                        foreach ($arr as &$items){ //筛选色系
                            if(substr($items['kind_val_sku'],0,1) == '0'){
                                $items['color'] = substr($items['kind_val_sku'],0,1).'1';
                            }else{
                                $items['color'] = substr($items['kind_val_sku'],0,1).'0';
                            }
                        }
                    }

                    $goods_config[$k]->config_msg = $arr;
                }
            }
            return view('admin.kind.kind_config_val')->with(compact('goods_kinds', 'goods_config'));
        } else if ($request->isMethod('post')) {
            //修改产品信息
            $kind_primary_id = $request->input('goods_kind_id');
            $goods_kind = goods_kind::where('goods_kind_id', $kind_primary_id)->first();
            if(!$goods_kind){
                return response()->json(['err' => '0', 'msg' => '产品属性不存在']);
            }
            $goods_kind->goods_kind_english_name = $request->input('goods_kind_english_name') ? $request->input('goods_kind_english_name') : '';
            $goods_kind->goods_kind_yn_name = $request->input('goods_kind_yn_name') ? $request->input('goods_kind_yn_name') : '';
            $goods_kind->goods_kind_volume = $request->input('width', 0) . 'cm*' . $request->input('depth', 0) . 'cm*' . $request->input('height', 0) . 'cm';
            $goods_kind->goods_kind_postage = $request->input('goods_kind_postage', 0) == null ? 0 : $request->input('goods_kind_postage', 0);
            $goods_kind->goods_kind_user_type = $request->input('goods_kind_user_type', 0) == null ? 0 : $request->input('goods_kind_user_type', 0);
            $img = $request->file('goods_kind_img');
            $img_name = '';
            if ($img) {
                $size = filesize($img);
                if ($size > 8 * 1024 * 1024) {
                    return response()->json(['err' => 0, 'str' => '赠品图片不能超过8M！']);
                }
                $file_name = $img->getClientOriginalName();
                $ext = $img->getClientOriginalExtension();
                $fileName = md5(uniqid($file_name));
                $newImagesName = 'fm' . "_" . $fileName . '.' . $ext;//生成新的的文件名
                $filedir = "upload/goods_kind/" . date('Ymd') . '/';
                $img->move($filedir, $newImagesName);
                $img_name = $filedir . $newImagesName;
                $goods_kind->goods_kind_img = $img_name;
            }
//            $goods_kind->goods_buy_url = $request->input('goods_buy_url');
//            $goods_kind->goods_buy_msg = $request->input('goods_buy_msg');
            $goods_kind->goods_buy_weight = $request->input('goods_buy_weight',0) == null ? 0 : $request->input('goods_buy_weight',0);
            /*$goods_kind->goods_kind_admin = Auth::user()->admin_id;*/
            $goods_kind->goods_product_id = $request->input('product_type_id');
            //1.验证字段是否漏填
            $goods_config_name = $request->input('goods_config_name');
            $data_null = false; //判断产品是否只有一个属性，并且为空，属性为空为true；
            $goods_config_color = [];
            $goods_color_sku = [];
            if(count($goods_config_name) > 3) {
                return response()->json(['err' => '0', 'msg' => '产品属性不能超过三组!']);
            }
            if ($goods_config_name) {
                foreach ($goods_config_name as $key=>$item) {
                    if ($data_null == false) {
                        if (!isset($item['goods_config_name']) || !$item['goods_config_name'] || !isset($item['msg']) || empty($item['msg']) || !isset($item['goods_config_english_name']) || !$item['goods_config_english_name']) {
                            return response()->json(['err' => '0', 'msg' => '产品属性名，产品英文属性名，属性值，属性英文名均不能为空!']);
                        }
                        foreach ($item['msg'] as $val) {
                            if(in_array($item['goods_config_name'],['颜色','顏色']) || strtolower($item['goods_config_english_name'] == 'color' || strtolower($item['goods_config_name']) == 'color')){
                                if (!$val['goods_config'] || !$val['goods_config_english'] || !$val['color']) {
                                    return response()->json(['err' => '0', 'msg' => '产品属性值、产品英文属性值、色系不能为空!']);
                                }
                            }else{
                                if (!$val['goods_config'] || !$val['goods_config_english']) {
                                    return response()->json(['err' => '0', 'msg' => '产品属性值、产品英文属性值不能为空!']);
                                }
                            }
                            if(isset($val['color']) && $val['color']){
                                $kind_val_sku = isset($val['kind_val_sku']) ? (substr($val['kind_val_sku'],0,1) == 0 ? substr($val['kind_val_sku'],0,1).'1' :  substr($val['kind_val_sku'],0,1).'0') : '';
                                $color_num = $kind_val_sku ? $kind_val_sku : $val['color'];
                                if(in_array($color_num,$goods_config_color)){
                                    $goods_color_sku[$color_num]++;
                                }else{
                                    $goods_color_sku[$color_num] = 0;
                                    array_push($goods_config_color,$color_num);
                                }
                            }
                            if (!isset($val['goods_config']) || !$val['goods_config'] || !isset($val['goods_config_english']) || !$val['goods_config_english']) {
                                return response()->json(['err' => '0', 'msg' => '产品属性值、产品英文属性值、色系不能为空!']);
                            }
                        }
                        if ((count($item['msg']) != count(array_unique(array_column($item['msg'], 'goods_config')))) || (count($item['msg']) != count(array_unique(array_column($item['msg'], 'goods_config_english'))))){
                            return response()->json(['err' => '0', 'msg' => '产品属性值或产品英文属性值不能重复!']);
                        }
                    }
                }
            }
            if (!$data_null && $goods_config_name) { //产品属性不为空
                //添加产品属性和产品属性值
                foreach ($goods_config_name as $item) {
                    if (isset($item['id'])) {
                        $kind_config = kind_config::where('kind_config_id', $item['id'])->first();
                    } else {
                        $kind_config = new kind_config();
                    }
                    $kind_config->kind_config_msg = $item['goods_config_name'];
                    $kind_config->kind_config_english_msg = $item['goods_config_english_name'] ? $item['goods_config_english_name'] : '';
                    $kind_config->kind_primary_id = $kind_primary_id;
                    $kind_config_bool = $kind_config->save();
                    $kind_config_id = $kind_config->kind_config_id;
                    //新增属性值
                    if ($kind_config_bool) {
                        foreach ($item['msg'] as $value) {
                            if (isset($value['id'])) {
                                $kind_val = kind_val::where('kind_val_id', $value['id'])->first();
                            } else {
                                $kind_val = new kind_val();
                            }
                            if(in_array($item['goods_config_name'],['颜色','顏色']) || strtolower($item['goods_config_english_name'] == 'color' || strtolower($item['goods_config_name']) == 'color')) {
                                $kind_val->kind_val_sku = isset($value['kind_val_sku']) ? $value['kind_val_sku'] : (isset($value['color']) ? skuSDK::get_color_sku($value['color'],$goods_color_sku) : '00');
                            }
                            $kind_val->kind_val_msg = $value['goods_config'];
                            $kind_val->kind_val_english_msg = $value['goods_config_english'] ? $value['goods_config_english'] : '';
                            $kind_val->kind_primary_id = $kind_primary_id;
                            $kind_val->kind_type_id = $kind_config_id;
                            $kind_val->save();
                        }
                    }
                }
            }
            //修改产品信息
            $goods_kind->save();
            $sku = new skuSDK($kind_primary_id, $goods_kind->goods_product_id,$goods_kind->goods_kind_user_type);
            $sku->set_sku_by_attr();
            if ($request->input('supplier_id') || $request->input('supplier_url') || $request->input('supplier_tel') || $request->input('supplier_contact') || $request->input('supplier_price') || $request->input('supplier_num') || $request->input('supplier_remark')) {
                if ($request->input('supplier_id')) {
                    $supplier = supplier::find($request->input('supplier_id'));
                } else {
                    $supplier = new supplier();
                }
                $supplier->supplier_url = $request->input('supplier_url', '');
                $supplier->supplier_tel = $request->input('supplier_tel', '');
                $supplier->supplier_contact = $request->input('supplier_contact', '');
                $supplier->supplier_price = $request->input('supplier_price') == null ? 0:  $request->input('supplier_price', 0);
                $supplier->supplier_num = $request->input('supplier_num', 0) == null ? 0 : $request->input('supplier_num', 0);
                $supplier->supplier_remark = $request->input('supplier_remark', '');
                $supplier->is_spots = $request->input('is_spots');
                $supplier->is_spare = 0;
                $supplier->goods_kind_primary_id = $kind_primary_id;
                $supplier->save();
            }
            if ($request->input('spare_supplier_id') || $request->input('spare_supplier_url') || $request->input('spare_supplier_tel') || $request->input('spare_supplier_contact') || $request->input('spare_supplier_price') || $request->input('spare_supplier_num') || $request->input('spare_supplier_remark')) {
                if ($request->input('spare_supplier_id')) {
                    $spare_supplier = supplier::find($request->input('spare_supplier_id'));
                } else {
                    $spare_supplier = new supplier();
                }
                $spare_supplier->supplier_url = $request->input('spare_supplier_url', '');
                $spare_supplier->supplier_tel = $request->input('spare_supplier_tel', '');
                $spare_supplier->supplier_contact = $request->input('spare_supplier_contact', '');
                $spare_supplier->supplier_price = $request->input('spare_supplier_price') == null ? 0:  $request->input('spare_supplier_price', 0);
                $spare_supplier->supplier_num = $request->input('spare_supplier_num', 0) == null ? 0 : $request->input('spare_supplier_num', 0);
                $spare_supplier->supplier_remark = $request->input('spare_supplier_remark', '');
                $spare_supplier->is_spots = $request->input('spare_supplier_is_spots');
                $spare_supplier->is_spare = 1;
                $spare_supplier->goods_kind_primary_id = $kind_primary_id;
                $spare_supplier->save();
            }
             //生成产品SKU
            $sku_sdk=new \App\channel\skuSDK($goods_kind->goods_kind_id,$request->input('product_type_id'),$request->input('goods_kind_user_type'));
            try{
                $mark=$sku_sdk->set_sku();
                if(!$mark) {
                    $goods_kind->delete();
                    return response()->json(['err' => '0', 'msg' => $sku_sdk->get_error()]);
                }
            }catch(\Exception $e){
                 return response()->json(['err' => '0', 'msg' => 'SKU生成失败！']);
            }
            
            $ip = $request->getClientIp();
            //加log日志
            operation_log($ip, $goods_kind->goods_kind_name . '产品修改成功');
            return response()->json(['err' => '1', 'msg' => '产品属性修改成功!']);
        }
    }

    /** 删除产品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delkind(Request $request)
    {
        $id = $request->input('id');
        $goods_kinds = goods_kind::where('goods_kind_id', $id)->first();
        if (!$goods_kinds) {
            return response()->json(['err' => '0', 'str' => '产品不存在!']);
        }
        $goods = goods::where('goods_kind_id', $id)->get();
        if (!$goods->isEmpty()) {
            return response()->json(['err' => '0', 'str' => '该产品名称已绑定商品，不可删除!']);
        }
        $goods_kind = goods_kind::where('goods_kind_id', $id)->delete();
        if ($goods_kind) {
            supplier::where('goods_kind_primary_id', $id)->delete();
            $ip = $request->getClientIp();
            //加log日志
            operation_log($ip, $goods_kinds->goods_kind_name . '产品删除成功');
            return response()->json(['err' => '1', 'str' => '删除成功!']);
        } else {
            return response()->json(['err' => '0', 'str' => '删除失败!']);
        }
    }

    /** 产品属性详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $goods_kinds_id = $request->input('id');
        $goods_config = \App\kind_config::where('kind_primary_id', $goods_kinds_id)->get();
        if ($goods_config != null) {
            foreach ($goods_config as $k => $v) {
                $arr = \App\kind_val::where('kind_type_id', $v->kind_config_id)->orderBy('kind_val_id', 'asc')->get()->toArray();
                $goods_config[$k]->config_msg = $arr;
            }
        }
        return view('admin.kind.show')->with(compact('goods_config'));
    }
    /**
     * 释放产品SKU
     * @param  Request $request 
     * @return \Illuminate\Http\JsonResponse
     */
    public function del_sku(Request $request)
    {
        $id=$request->input('id');
        $goods_ids=goods::select('goods_id')->
        where(function($query)use($id){
            $query->where('goods_kind_id',$id);
            $query->where('is_del',0);
        })
        ->get();
        $using_ids=[];
        foreach($goods_ids as $k => $v){
            $url=\App\url::where('url_goods_id',$v->goods_id)->orWhere('url_zz_goods_id',$v->goods_id)->first();
            if($url!=null) $using_ids[]=$v->goods_id;
        }
        if($using_ids!=null)  return response()->json(['err' => '0', 'str' => '释放失败!id为'.implode(',',$using_ids).'的产品有域名绑定，请先解绑域名！']);
        $goods_kind=\App\goods_kind::where('goods_kind_id',$id)->first();
        $last_kind=\App\goods_kind::select('goods_kind_id')->where('goods_product_id',$goods_kind->goods_product_id)->orderBy('goods_kind_time','desc')->first();
        if($last_kind->goods_kind_id==$goods_kind->goods_kinds_id) return response()->json(['err' => '0', 'str' => '释放失败!无法释放改品类下最后一个产品，请选择其它产品释放']);
        $goods_kind->goods_kind_sku_status=1;
        $msg=$goods_kind->save();
        if ($msg) {
            if(\App\sku_free::where('sku_free_msg',$goods_kind->goods_kind_sku)->first()==null){
                $sku_free=new \App\sku_free;
                $sku_free->sku_free_type=$goods_kind->goods_product_id;
                $sku_free->sku_free_msg=$goods_kind->goods_kind_sku;
                $sku_free->sku_free_time=date("Y-m-d H:i:s", time());
                $sku_free->save();
            }
            return response()->json(['err' => '1', 'str' => '释放成功!']);
        } else {
            return response()->json(['err' => '0', 'str' => '释放失败!数据操作错误']);
        }
    }
    public function sku_show(Request $request)
    {
        $id=$request->get('id');
        $goods_kind=\App\goods_kind::where('goods_kind_id',$id)->first();
        $goods_kind->attrs = DB::table('kind_config as kc')->join('kind_val as kv', 'kc.kind_config_id', 'kv.kind_type_id', 'join')
            ->where('kc.kind_primary_id',$goods_kind->goods_kind_id)->orderBy('kv.kind_type_id')->get();
        return view('admin.kind.sku_show')->with(compact('goods_kind'));
    }
    public function sku_search(Request $request)
    {
        if($request->isMethod('get')){
            return view('admin.kind.sku_search');
        }elseif($request->isMethod('post')){
            $sku=trim($request->input('sku'));
            if($sku==null||strlen($sku)<4){
                return '<span style="color:red;">SKU码必须为4位或10位！</span>';
            }
            if(strlen($sku)!=4&&strlen($sku)!=10){
                return '<span style="color:red;">SKU码必须为4位或10位！</span>';
            }
            $kind_sku=substr($sku, 0,4);
            $attr_sku = substr($sku,-6);
            $goods_kinds=goods_kind::where('goods_kind_sku',$kind_sku)->get();


            if($goods_kinds->count()<=0){
                return '<span style="color:red;">无对应数据！</span>';
            }
            foreach ($goods_kinds as $goods_kind){
                $goods_kind->attrs = DB::table('kind_config as kc')->join('kind_val as kv', 'kc.kind_config_id', 'kv.kind_type_id', 'join')
                    ->where('kc.kind_primary_id',$goods_kind->goods_kind_id)->orderBy('kv.kind_type_id')->get();
//                dd($goods_kind->attrs);
//                $goods_kind->attrs = kind_config::with('vals')->where('kind_primary_id',$goods_kind->goods_kind_id)->get();
                $skuSDK = new skuSDK($goods_kind->goods_kind_id, $goods_kind->product_type_id,$goods_kind->goods_kind_user_type);
                $current_attrs = $skuSDK->get_attr_by_sku($attr_sku);
                $goods_kind->current_attrs = array_column($current_attrs, 'kind_val_id');
            }
            return view('admin.kind.sku_ajax')->with(compact('goods_kinds'));
        }
    }

    /**
     * 产品导出
     * @param Request $request
     * @return string
     */
    public function outkind(Request $request)
    {
        if(strtotime($request->input('max'))-strtotime($request->input('min'))>864000){
            return '<span style="color:red;display:block;width:100%;text-align:center;">最多导出十天数据！(三秒后自动返回上个页面)<span><script>setTimeout("window.history.go(-1)",3000); </script>';
        }

        //订单导出
        $data=goods_kind::leftjoin('admin','goods_kind.goods_kind_admin','=','admin.admin_id')
            ->where(function($query)use($request){

                //按照个人权限筛选
                if(Auth::user()->is_root!='1'){
                    $query->whereIn('goods_kind.goods_kind_admin', admin::get_admins_id());
                }

                //时间筛选
                if($request->input('min')&&$request->input('max')){
                    $query->whereBetween('goods_kind.goods_kind_time',[$request->input('min').' 00:00:00',$request->input('max').' 00:00:00']);
                }else{ //默认近10天
                    $now_date=date('Y-m-d',time()+60*60*24).' 00:00:00';
                    $start_date=date('Y-m-d',time()-9*60*60*24).' 00:00:00';
                    $query->whereBetween('goods_kind.goods_kind_time',[$start_date,$now_date]);
                }

                //条件筛选
                if($request->has('search') && trim($request->input('search'))){
                    $search = trim($request->input('search'));
                    if($request->has('search') && $search){
                        $query->where('goods_kind.goods_kind_name', 'like', "%$search%");
                        $query->orWhere('goods_kind.goods_kind_sku', 'like', "$search%");
                        $query->orWhere('goods_kind.goods_kind_english_name', 'like', "%$search%");
                    }
                }

                //产品分类
                if ($request->has('product_type_id') && $request->input('product_type_id') != 0) {
                    $query->where('goods_product_id', $request->input('product_type_id'));
                }
            })
            ->orderBy('goods_kind.goods_kind_id','desc')
            ->get()->toArray();
        if(count($data) > 80){
            return '<span style="color:red;display:block;width:100%;text-align:center;">导出数据过多，请缩短筛选时间并保持数据在80条以内！(三秒后自动返回上个页面)<span><script>setTimeout("window.history.go(-1)",3000); </script>';
        }
        if(count($data) <= 0){
            return '<span style="color:red;display:block;width:100%;text-align:center;">数据为空，无法导出数据！(三秒后自动返回上个页面)<span><script>setTimeout("window.history.go(-1)",3000); </script>';
        }
        if($request->has('min')&&$request->has('max')){
            $min = date('Y年m月d',strtotime($request->input('min')));
            $max = date('Y年m月d',strtotime($request->input('max')));
            $filename= $min.'=>'.$max.'订单记录';
        }else{
            $filename='订单记录'.date("Y年m月d日");
        }
        $cellData[] = ['产品名称','产品英文名','产品图片','产品录入时间','产品种类','产品供应商链接','商品属性','商品SKU'];

        Excel::create($filename,function ($excel) use ($cellData,$filename,$data){
            $excel->sheet($filename, function ($sheet) use ($cellData,$data){
                $sheet->rows($cellData);
                $num = 2;
                foreach ($data as $key=>$v)
                {
                    //产品属性信息
                    $product_attr = goods_kind::attr_cartesian_product($v['goods_kind_id']);
                    //获取产品前四位SKU
                    $first_four_num = goods_kind::where('goods_kind_id',$v['goods_kind_id'])->value('goods_kind_sku');
                    $config_num = count($product_attr);
                    if($config_num != 0){
                        $sheet->setMergeColumn([
                            'columns' => ['A', 'B', 'C', 'D', 'E', 'F'],
                            'rows' => [
                                [$num, $num+$config_num-1],
                            ],
                        ]);
                    }

                    // 设置多个列
                    $sheet->setWidth([
                        'A' => 30,
                        'B' => 50,
                        'C' => 30,
                        'D' => 30,
                        'E' => 30,
                        'F' => 50,
                        'G' => 50,
                        'H' => 50,
                    ]);
                    // 总分 右对齐
                    $sheet->cells('G:H', function($cells) {
                        $cells->setAlignment('left');
                    });

                    if($config_num == 0){
                        $sheet->cell('G'.$num,'');
                        $sheet->cell('H'.$num,$first_four_num.'000000');
                    }else{
                        for($j = 0;$j<$config_num;$j++) {
                            $sku_value = $first_four_num.$product_attr[$j]['sku'];
                            $sheet->cell('G'.($num+$j),rtrim($product_attr[$j]['val'],','));
                            $pattern='/e/';
                            if(preg_match($pattern,$sku_value)){
                                $sheet->cell('H'.($num+$j),$sku_value." ");
                            }else{
                                $sheet->cell('H'.($num+$j),$sku_value);
                            }
                        }
                    }
                    $sheet->cell('A'.$num,$v['goods_kind_name']);
                    $sheet->cell('B'.$num,$v['goods_kind_english_name']);
                    //判断文件是否存在
                    if(file_exists($v['goods_kind_img'])){
                        $objDrawing = new \PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath($v['goods_kind_img']);
                        $objDrawing->setCoordinates('C' . $num);
                        $objDrawing->setHeight(80);
                        $objDrawing->setOffsetX(1);
                        $objDrawing->setRotation(1);
                        $objDrawing->setWorksheet($sheet);
                    }else{
                        $sheet->cell('C'.$num,'');
                    }

                    $sheet->cell('D'.$num,$v['goods_kind_time']);
                    $sheet->cell('E'.$num,product_type::where('product_type_id',$v['goods_product_id'])->value('product_type_name'));
                    $sheet->cell('F'.$num,supplier::where('goods_kind_primary_id',$v['goods_kind_id'])->value('supplier_url'));
                    if($config_num == 0){
                        $num ++;
                    }else{
                        $num += $config_num;
                    }
                }
            });
        })->export('xls');
    }
}
