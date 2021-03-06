<?php

namespace App\Http\Controllers\admin;

use App\admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\goods;
use App\url;
use DB;
use Illuminate\Support\Facades\Auth;
class UrlController extends Controller
{
    /** 域名首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function goods_url(){
        $counts=DB::table('url')->count();
    	return view('admin.url.goods_url')->with('counts',$counts);
    }

    /** url链接列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_url(Request $request){
    		$info=$request->all();
        	$cm=$info['order'][0]['column'];
	        $dsc=$info['order'][0]['dir'];
	        $order=$info['columns']["$cm"]['data'];
	        $draw=$info['draw'];
	        $start=$info['start'];
	        $len=$info['length'];
	        $search=trim($info['search']['value']);
	        $counts=DB::table('url')
	        ->count();
            $newcount=DB::table('url')
            ->select('url.*')
            ->where(function($query)use($search){
                $query->where('url.url_url','like',"%$search%");
                $query->orWhere(function($query)use($search){
                    $query->whereIn('url.url_goods_id',\App\goods::get_search_arr($search));
                });
                $query->orWhere(function($query)use($search){
                    $query->whereIn('url.url_zz_goods_id',\App\goods::get_search_arr($search));
                });
                $query->orWhere(function($query)use($search){
                    $query->whereIn('url.url_site_id',\App\site::get_search_arr($search));
                });
            })
            ->where(function($query)use($request){
                if($request->input('url_flag_fb')!=0){
                    $query->where('url.url_flag','like',"%0%");
                }
                if($request->input('url_flag_yahoo')!=0){
                    $query->where('url.url_flag','like',"%1%");
                }
                if($request->input('url_flag_google')!=0){
                    $query->where('url.url_flag','like',"%2%");
                }
                $bind_status = $request->input('bind_status');
                switch ($bind_status){
                    case '1': //绑定单品
                        $query->whereNotNull('url.url_goods_id');
                        $query->whereNull('url.url_zz_goods_id');
                        break;
                    case '2': //绑定遮罩
                        $query->whereNull('url.url_goods_id');
                        $query->whereNotNull('url.url_zz_goods_id');
                        break;
                    case '3': //绑定遮罩和域名
                        $query->whereNotNull('url.url_goods_id');
                        $query->whereNotNull('url.url_zz_goods_id');
                        break;
                    case '4': //未绑定商品
                        $query->whereNull('url.url_goods_id');
                        $query->whereNull('url.url_zz_goods_id');
                        break;
                    case '5'://绑定站点
                        $query->whereNotNull('url.url_site_id');
                        break;
                }
            })
            ->where(function($query){
                $query->whereIn('url.url_admin_id',admin::get_admins_id());
            })
            ->count();


	        $data=DB::table('url')
	        ->select('url.*')
            ->where(function($query)use($search){
                $query->where('url.url_url','like',"%$search%");
                $query->orWhere(function($query)use($search){
                  $query->whereIn('url.url_goods_id',\App\goods::get_search_arr($search));
                });
                $query->orWhere(function($query)use($search){
                  $query->whereIn('url.url_zz_goods_id',\App\goods::get_search_arr($search));
                });
                 $query->orWhere(function($query)use($search){
                    $query->whereIn('url.url_site_id',\App\site::get_search_arr($search));
                });
            })
            ->where(function($query)use($request){
              if($request->input('url_flag_fb')!=0){
                $query->where('url.url_flag','like',"%0%");
              }
              if($request->input('url_flag_yahoo')!=0){
                $query->where('url.url_flag','like',"%1%");
              }
              if($request->input('url_flag_google')!=0){
                $query->where('url.url_flag','like',"%2%");
              }
              $bind_status = $request->input('bind_status');
              switch ($bind_status){
                  case '1': //绑定单品
                      $query->whereNotNull('url.url_goods_id');
                      $query->whereNull('url.url_zz_goods_id');
                      break;
                  case '2': //绑定遮罩
                      $query->whereNull('url.url_goods_id');
                      $query->whereNotNull('url.url_zz_goods_id');
                      break;
                  case '3': //绑定遮罩和域名
                      $query->whereNotNull('url.url_goods_id');
                      $query->whereNotNull('url.url_zz_goods_id');
                      break;
                  case '4': //未绑定商品
                      $query->whereNull('url.url_goods_id');
                      $query->whereNull('url.url_zz_goods_id');
                      break;
                  case '5'://绑定站点
                        $query->whereNotNull('url.url_site_id');
                        break;
                }
            })
            ->where(function($query){
                $query->whereIn('url.url_admin_id',admin::get_admins_id());
            })
	        ->orderBy($order,$dsc)
	        ->offset($start)
	        ->limit($len)
	        ->get();
	        if(!$data->isEmpty()){
                foreach($data as $key => $v) {
                    $url_goods=\App\goods::where('goods_id',$v->url_goods_id)->first();
                    $url_zz_goods=\App\goods::where('goods_id',$v->url_zz_goods_id)->first();
                    $url_site=\App\site::where('sites_id',$v->url_site_id)->first();
                    if($url_goods!=null){
                        $data[$key]->url_goods_id=$url_goods->goods_real_name;
                    }
                    if($url_zz_goods!=null){
                        $data[$key]->url_zz_goods_id=$url_zz_goods->goods_real_name;
                    }
                    if($url_site!=null){
                        $data[$key]->url_site_id=$url_site->sites_name;
                        $data[$key]->url_site_blade=\App\admin::getBladeName($url_site->sites_blade_type);
                    }
                    $data[$key]->url_flag=explode(',',$v->url_flag);
                }
            }
	        $arr=['draw'=>$draw,'recordsTotal'=>$counts,'recordsFiltered'=>$newcount,'data'=>$data];
	        return response()->json($arr);
    }

    /** 添加域名
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function url_add(Request $request){
      //添加域名
      if($request->isMethod('get')){
        return view('admin.url.url_add');
      }elseif($request->isMethod('post')){
        $data=$request->all();
        $isalive=url::where('url_url',$data['url_url'])->first();
          if($isalive!=null){
                 return response()->json(['err'=>0,'str'=>'添加失败！该域名已存在！']);
          }
        $url=new url;
        $url->url_url=$data['url_url'];
        $url->url_zz_level=$data['url_level'];
        $url->url_zz_for=$data['url_for'];
        $url->url_admin_id=Auth::user()->admin_id;
        if(isset($data['ad_account'])){
            $url->url_ad_account_id=implode(',', $data['ad_account']);
        }else{
           $url->url_ad_account_id=null;
        }
        if(isset($data['is_online'])&&$data['is_online']!=null){
          $url->url_type='1';
        }else{
          $url->url_type='0';
        }
        $msg=$url->save();
        if($msg)
         {
             $ip = $request->getClientIp();
             //加log日志
             operation_log($ip,'添加域名成功,域名：'.$data['url_url']);
                  return response()->json(['err'=>1,'str'=>'添加成功！']);
         }else{
                  return response()->json(['err'=>0,'str'=>'添加失败！']);
         }
      }
      
    }

    /** 域名配置弹框页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
   public function churl(Request $request){
   		
   		$url=url::where('url_id',$request->id)->first();
      $ad_account=\App\ad_account::all();
      $belong=explode(',', $url['url_ad_account_id']);
      foreach($ad_account as $k => $v){
        if( in_array($v->ad_account_id, $belong)){
          $ad_account[$k]->is_belong=true;
        }else{
          $ad_account[$k]->is_belong=false;
        }
      }
      if(isset($goods)){
        return view('admin.url.churl')->with(compact('goods','url','ad_account'));
      }else{
        return view('admin.url.churl')->with(compact('url','ad_account'));
      }
   }

    /** 域名配置操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function ajaxup(Request $request){
      //修改域名配置信息
//   	    $msg=$request->all();
   	    $msg=$request->except('_token');
        if(isset($msg['url_site_id'])&&$msg['url_site_id']>0){
          $msg['url_site_id']=(int)$msg['url_site_id'];
        }else{
          $msg['url_site_id']=null;
        }
        if(isset($msg['url_goods_id'])&&$msg['url_goods_id']=='null'){
          unset($msg['url_goods_id']);
        }
        if(isset($msg['url_zz_goods_id'])&&$msg['url_zz_goods_id']=='null'){
          unset($msg['url_zz_goods_id']);
        }
   	    $url=url::where('url_id',$msg['url_id'])->first();
   	    if($url==null){
   	    	$url=new url();
          if(isset($msg['url_site_id'])){
            $url->url_site_id=$msg['url_site_id'];
          }else{
             $url->url_goods_id=$msg['id'];
          }
          $url->url_admin_id=Auth::user()->admin_id;
   	    	$url->url_url=$msg['url_url'];
   	    	$url->url_type=$msg['url_type'];
   	    	$msg=$url->save();
   	    	if($msg){
   	    		return json_encode(true);
   	    	}else{
   	    		return json_encode(false);
   	    	}
   	    }elseif(isset($msg['url_site_id'])){
          $code=\App\goods::where(function($query)use($url){
            $query->where('goods_id',$url->url_goods_id);
            $query->orWhere('goods_id',$url->url_zz_goods_id);
          })
          ->update(['bd_type'=>'0']);
          //if($code==false) return response()->json(['err'=>0,'str'=>'更改失败！域名原有产品页解除绑定失败！']);
          $url->url_goods_id=null;
          $url->url_zz_goods_id=null; 
          $url->url_site_id=$msg['url_site_id'];
          $url->url_url=$msg['url_url'];
          $url->url_type=$msg['url_type'];
          $url->url_zz_level=$msg['url_zz_level'];
          $url->url_zz_for=$msg['url_zz_for'];
          $url->url_admin_id=Auth::user()->admin_id;
          $url->url_ad_account_id=isset($msg['ad_account'])?implode(',', $msg['ad_account']):null;
          $code=$url->save();
          if($code==false){
            return response()->json(['err'=>0,'str'=>'更改失败！域名数据更新失败！']);
          } 
          return response()->json(['err'=>1,'str'=>'更改成功！站点绑定成功!']);
        }else{
          $isalive=url::where('url_url',$msg['url_url'])->first();
          if($isalive!=null&&$isalive->url_id!=$url->url_id){
                 return response()->json(['err'=>0,'str'=>'更改失败！该域名已存在！']);
          }
          if(isset($msg['url_goods_id'])&&isset($msg['url_zz_goods_id'])&&($msg['url_goods_id']==$msg['url_zz_goods_id'])){
              if($msg['url_goods_id']==null&&$msg['url_zz_goods_id']==null){
                //清空域名商品绑定
              }else{
               //选择同一商品绑定
                 return response()->json(['err'=>0,'str'=>'添加失败！遮罩单品不得与正常单品相同！']);
              }
                 
          }
          //撤销原商品的绑定状态
          if(!isset($msg['url_goods_id'])){
            $oldid=$url->url_goods_id;
            if($oldid!=null){
              $xxgoods=\App\goods::where('goods_id',$oldid)->first();
              $xxgoods->bd_type='0';
              $xxgoods->save();
            }
          }
          if(!isset($msg['url_zz_goods_id'])){
            $oldids=$url->url_zz_goods_id;
            if($oldids!=null){
              $xxgoodss=\App\goods::where('goods_id',$oldids)->first();
              $xxgoodss->bd_type='0';
              $xxgoodss->save();
            }
          }
          //遮罩单品与正常单品位置互换
          if(isset($msg['url_goods_id'])&&isset($msg['url_zz_goods_id'])&&$msg['url_goods_id']==$url->url_zz_goods_id&&$msg['url_zz_goods_id']==$url->url_goods_id){
            $zz_goods=\App\goods::where('goods_id',$msg['url_zz_goods_id'])->first();
            $zz_goods->bd_type='1';
            $zz_goods->save();
            $zc_goods=\App\goods::where('goods_id',$msg['url_goods_id'])->first();
            $zc_goods->bd_type='2';
            $zc_goods->save();
          }else{
                if(isset($msg['url_goods_id'])){
                  $bd_type=\App\goods::where('goods_id',$msg['url_goods_id'])->first();
                  if($bd_type!=null&&$bd_type->bd_type!=0&&$url->url_goods_id!=$msg['url_goods_id']){
                    //检测此正常商品是否被已经绑定到某个域名下
                      $url_msg=\App\url::where(function($query)use($msg){
                         $query->where('url_goods_id',$msg['url_goods_id']);
                         $query->orWhere('url_zz_goods_id',$msg['url_goods_id']);
                      })
                      ->first();
                      if($url_msg!=null){
                            return response()->json(['err'=>0,'str'=>'更改失败！被选中正常单品已处于绑定状态']);
                      }
                  }
                  $bd_type->bd_type='1';
                  $bd_type->save();
                }
                 if(isset($msg['url_zz_goods_id'])){
                   $bd_type=\App\goods::where('goods_id',$msg['url_zz_goods_id'])->first();
                  if($bd_type!=null&&$bd_type->bd_type!=0&&$url->url_zz_goods_id!=$msg['url_zz_goods_id']){
                    //检测此遮罩商品是否被已经绑定到某个域名下
                     $url_msg=\App\url::where(function($query)use($msg){
                         $query->where('url_goods_id',$msg['url_zz_goods_id']);
                         $query->orWhere('url_zz_goods_id',$msg['url_zz_goods_id']);
                      })
                      ->first();
                      if($url_msg!=null){
                            return response()->json(['err'=>0,'str'=>'更改失败！被选中遮罩单品已处于绑定状态']);
                      }
                  }
                  $bd_type->bd_type='2';
                  $bd_type->save();
                }
          }
          $url->url_goods_id=isset($msg['url_goods_id'])?$msg['url_goods_id']:null;
   	    	$url->url_zz_goods_id=isset($msg['url_zz_goods_id'])?$msg['url_zz_goods_id']:null;
   	    	$url->url_url=$msg['url_url'];
          $url->url_type=$msg['url_type'];
          $url->url_zz_level=$msg['url_zz_level'];
   	    	$url->url_zz_for=$msg['url_zz_for'];
          $url->url_admin_id=Auth::user()->admin_id;
          $url->url_ad_account_id=isset($msg['ad_account'])?implode(',', $msg['ad_account']):null;
          $url->url_site_id=null;
   	    	$bool=$url->save();
   	    	if($bool)
         {
             $ip = $request->getClientIp();
             //加log日志
             operation_log($ip,'域名配置成功,域名：'.$msg['url_url'].', 绑定商品：'.(isset($msg['url_goods_id']) ? goods::where('goods_id',$msg['url_goods_id'])->value('goods_name') : '---').', 遮罩商品'.(isset($msg['url_zz_goods_id']) ? goods::where('goods_id',$msg['url_zz_goods_id'])->value('goods_name') : '---'),json_encode($msg));
             return response()->json(['err'=>1,'str'=>'更改成功！']);
         }else{
             return response()->json(['err'=>0,'str'=>'更改失败！']);
         }
   	    }
   }

    /** 添加广告账户
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
   public function add_account(Request $request)
   {
    if($request->isMethod('get')){
      return view('admin.url.add_account');
    }else if($request->isMethod('post')){
      $data=$request->all();
      if($data['ad_account_name']==null||$data['ad_account_type']==null||$data['ad_account_belong']==null){
                  return response()->json(['err'=>0,'str'=>'数据非法！']);
      }
      if(\App\ad_account::where('ad_account_name',$data['ad_account_name'])->first()!=null){
                  return response()->json(['err'=>0,'str'=>'账户名已存在！']);
      }
      $ad_account=new \App\ad_account;
      $ad_account->ad_account_name=$data['ad_account_name'];
      $ad_account->ad_account_type=$data['ad_account_type'];
      $ad_account->ad_account_belong=$data['ad_account_belong'];
      $msg=$ad_account->save();
      if($msg)
         {
             $ip = $request->getClientIp();
             //加log日志
             operation_log($ip,'添加广告账户成功,账户名：'.$data['ad_account_name']);
                  return response()->json(['err'=>1,'str'=>'更改成功！']);
         }else{
                  return response()->json(['err'=>0,'str'=>'更改失败！']);
         }
    }
   }

    /** 修改广告账户
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
   public function update_account(Request $request)
   {
    if($request->isMethod('get')){
      return view('admin.url.update_account');
    }elseif($request->isMethod('post')){
      $data=$request->all();
      if(!isset($data['ad_account_id'])||!isset($data['ad_account_name'])||!isset($data['ad_account_belong'])||!isset($data['ad_account_type'])){
                  return response()->json(['err'=>0,'str'=>'数据非法！']);
      }
      $ad_account=\App\ad_account::where('ad_account_id',$data['ad_account_id'])->first();
      $old_type=$ad_account->ad_account_type;
      if($data['ad_account_type']==1){
        if($old_type==0||$ad_account->ad_account_belong!=$data['ad_account_belong']){
          foreach(\App\url::all() as $k => $v){
            $arr=explode(',', $v->url_ad_account_id);
            if(in_array($data['ad_account_id'], $arr)){
             $arr1=explode(',', $v->url_flag);
             if(!in_array($data['ad_account_belong'],$arr1)){
              array_push($arr1, $data['ad_account_belong']);
              sort($arr1);
              $v->url_flag=ltrim(implode(',', $arr1),',');
              $v->save();
              $change=true;
             }
            }
          }
        }
      }
      $ad_account->ad_account_name=$data['ad_account_name'];
      $ad_account->ad_account_type=$data['ad_account_type'];
      $ad_account->ad_account_belong=$data['ad_account_belong'];
      $msg=$ad_account->save();
      if($msg)
         {
             $ip = $request->getClientIp();
             //加log日志
             operation_log($ip,'修改广告账户成功,账户名：'.$data['ad_account_name']);
              if(isset($change)){
                  return response()->json(['err'=>1,'str'=>'更改成功！关联域名已被标记']);
              }
                  return response()->json(['err'=>1,'str'=>'更改成功！']);
         }else{
                  return response()->json(['err'=>0,'str'=>'更改失败！']);
         }
    }
   }

   public function ajax_account(Request $request)
   {
      $id=$request->input('id');
      $ad_account=\App\ad_account::where('ad_account_id',$id)->first();
      if($ad_account!=null&&$ad_account!=false){
           return response()->json(['err'=>1,'data'=>$ad_account]);
      }else{
           return response()->json(['err'=>0,'data'=>'未找到对应数据！']);
      }
   }
   public function clear_flag(Request $request)
   {
    $id=$request->input('id');
    $msg=\App\url::where('url_id',$id)->update(['url_flag'=>null]);
       if($msg)
         {
                  return response()->json(['err'=>1,'str'=>'清除成功！']);
         }else{
                  return response()->json(['err'=>0,'str'=>'清除失败！']);
         }
   }
   public function url_goods_ajax(Request $request)
   //域名下拉列表ajax接口
   {
    if(!$request->has('url_id')||!$request->has('type')||!$request->has('msg')){
       return response()->json(['err'=>0,'data'=>'缺少参数！']);
    }
    if($request->input('url_id')==null||$request->input('type')==null||$request->input('msg')==null){
       return response()->json(['err'=>0,'data'=>'缺少参数！']);
    }
    $type=$request->input('type');
    if($request->input('msg')=='false'){
      $msg='';
    }else{
      $msg=$request->input('msg');
    }
    $url=\App\url::where('url_id',$request->input('url_id'))->first();
    $arr=\App\admin::get_goods_id();
    $goods=\App\goods::whereIn('goods_id',$arr)
    ->where(function($query)use($msg){
      $query->where('goods_real_name','like',"%$msg%");
    })
    ->where(function($query){
      $query->where('is_del','0');
    })
    ->get(['goods_id','goods_real_name']);
    foreach ($goods as $key => $value) {
      if($type==1){
        if($value->goods_id==$url->url_goods_id){
          $goods[$key]->is_check=true;
        }else{
          $goods[$key]->is_check=false;
        }
      }elseif($type==2){
        if($value->goods_id==$url->url_zz_goods_id){
          $goods[$key]->is_check=true;
        }else{
          $goods[$key]->is_check=false;
        }
      }
    }
     return response()->json(['err'=>1,'data'=>$goods]);
   }
}
