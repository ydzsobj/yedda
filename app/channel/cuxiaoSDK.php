<?php
namespace App\channel;
use App\goods;
use App\cuxiao;
use App\special;
use Illuminate\Http\Request;
class cuxiaoSDK{
	public $goods;
	public $cuxiao;
	public $cuxiaos;
	public function __construct(goods $goods){
       $this->goods=$goods;
       $this->cuxiao=cuxiao::where('cuxiao_goods_id',$goods->goods_id)->orderBy('cuxiao_id','asc')->first();
       $this->cuxiaos=cuxiao::where('cuxiao_goods_id',$goods->goods_id)->orderBy('cuxiao_id','asc')->get();
	}
	public function getdiv(){
		//获取购物车页面div
		    $type=$this->goods->goods_cuxiao_type;
		    $goods=$this->goods;
		    $cuxiao=$this->cuxiao;
		    $cuxiaos=$this->cuxiaos;
		    $special=special::where('special_goods_id',$goods->goods_id)->get();
		    switch ($type) {
		    	case '0':
		    		return view('ajax.ajaxreturn')->with(compact('goods','cuxiao'));
		    		break;
		    	case '2':
		    		return view('ajax.ajaxreturn2')->with(compact('goods','cuxiao','special'));
		    		break;
		    	case '3':
		    	    return view('ajax.ajaxreturn3')->with(compact('goods','cuxiao','special','cuxiaos'));
		    	    break;
		    	default:
		    		# code...
		    		break;
		    }
        	return view('ajax.ajaxreturn')->with(compact('goods','cuxiao'));
	}
	public function get_price($num,$cuxiao_id=0){
		$type=$this->goods->goods_cuxiao_type;
		$goods=$this->goods;
           switch ($type) {
           	case '0':
           		$goods_price=$num*$goods->goods_price;
//           		$price=$num*$goods->goods_price;
//           		return $price;
           		break;
           	case '2':
           		$cuxiao=$this->cuxiao;
           		$arr=explode(',', $cuxiao->cuxiao_config);
           		$price=$goods->goods_price;
           		$end_price=$price*$num;
           		$jjp=0;
           		if($num<$arr[0]){
//           			return $num*$price;
                    $goods_price = $num*$price;
           		}
           		$jp=$price*($arr[0]-1);
           		for ($i=0; $i <count($arr)/2; $i++) { 
           			if($num>=($arr[$i*2])){
						if(isset($arr[$i*2+2])&&$num<($arr[$i*2+2])){
							$jjp+=($num-(($arr[$i*2])-1))*($price-(($arr[$i*2+1])));
							break;
						}else if(isset($arr[$i*2+2])&&$num>=($arr[$i*2+2])){
							$jjp+=(($arr[$i*2+2])-($arr[$i*2]))*($price-($arr[$i*2+1]));
						}else{
							$jjp+=($num-($arr[$i*2])+1)*($price-($arr[$i*2+1]));
						}
					}
           		}
                $goods_price=$jjp+$jp;
//           		$end_price=$jjp+$jp;
//           		return $end_price;
           		break;

           	case '3':
           	    if($cuxiao_id){
                    $cuxiao=cuxiao::where('cuxiao_id',$cuxiao_id)->first();
                    if(!$cuxiao){
                        return false;
                    }
                    $cuxiao_config=  $cuxiao->cuxiao_config;
                    $msg=explode(',', $cuxiao_config);
                    if($num==$msg[0]){
                        $goods_price =  $msg[1];
                        break;
                    }
                }else{
                    $cuxiao=cuxiao::where('cuxiao_goods_id',$goods->goods_id)->orderBy('cuxiao_id','asc')->get();
                    foreach($cuxiao as $v){
                        $msg=explode(',', $v->cuxiao_config);
                        if($num==$msg[0]){
//                     	return $msg[1];
                            $goods_price =  $msg[1];
                            break;
                        }
                    }
                    if(!isset($goods_price)){
                        return false;
                    }
                }

           	     //return false;
           	     break;
           	default:
           		return false;
           		break;
           }
           //计算商品差额
           return sprintf('%.2f',$goods_price) ;
	}

	
	
	public static function getcuxiaohtml($id,$goods_id){
	//获取后台页面中的更改div
		$goods=\App\goods::where('goods_id',$goods_id)->first();
		$cuxiao=\App\cuxiao::where('cuxiao_goods_id',$goods_id)->get();
		switch ($id) {
			case '0':
				$html='<div style="width:50%;margin:0px auto;color:red;">此促销类型无需配置信息</div>';
				return $html;
				break;
			case '1':
				$html='<div style="width:50%;margin:0px auto;color:red;">此促销类型还未开放,请选择其它活动类型。</div>';
				return $html;
				break;
			case '2':
				$cuxiao=\DB::table('cuxiao')
				->select('cuxiao.*','special.*','price.*')
				->leftjoin('special','cuxiao.cuxiao_special_id','=','special.special_id')
				->leftjoin('price','special.special_price_id','=','price.price_id')
				->where([['cuxiao_goods_id',$goods_id],['cuxiao_type','2']])
				->first();
				if($cuxiao!=null){
					$data=explode(',', $cuxiao->cuxiao_config);
					$arr=[];
					for ($i=0; $i <count($data)/2 ; $i++) { 
						$arr[$i]['num']=$data[$i*2];
						$arr[$i]['price']=$data[$i*2+1];
					}
					$cuxiao->cuxiao_config=$arr;
				}
				return view('admin.goods.channel2')->with(compact('cuxiao'));
				break;
			case '3':
				$cuxiao=\DB::table('cuxiao')
				->select('cuxiao.*','special.*','price.*')
				->leftjoin('special','cuxiao.cuxiao_special_id','=','special.special_id')
				->leftjoin('price','special.special_price_id','=','price.price_id')
				->where([['cuxiao_goods_id',$goods_id],['cuxiao_type','3']])
				->orderBy('cuxiao_id','asc')
				->get();
				
				return view('admin.goods.channel3')->with(compact('cuxiao'));
				break;
			
			default:
				
				break;
		}
	}
	public static function saveadd(Request $request,$goods_id){
		$type=$request->input('goods_cuxiao_type');
		switch($type){
			case '0':
			\DB::table('cuxiao')->where('cuxiao_goods_id',$goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods_id)->delete();
				return true;
				break;
			case '1':
			\DB::table('cuxiao')->where('cuxiao_goods_id',$goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods_id)->delete();
				return true;
				break;
			case '2':
					\DB::table('cuxiao')->where('cuxiao_goods_id',$goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods_id)->delete();
			$data=$request->all();
			$config="";
				$numarr=[];
				$pricearr=[];
				if(isset($data['cuxiao_num'])&&$data['cuxiao_num']!=null){
					foreach ($data['cuxiao_num'] as $key => $value) {
						$numarr[]=$value;
						$pricearr[]=$data['cuxiao_prize'][$key];
						//$config.=$value.','.$data['cuxiao_prize'][$key];
					}
				}
				if(isset($data['new_cuxiao'])&&$data['new_cuxiao']!=null){
					foreach($data['new_cuxiao'] as $key => $v){
						if($v['num']!=''&&$v['price']!=''&&$v['num']!=null&&$v['price']!=''){
								$numarr[]=$v['num'];
								$pricearr[]=$v['price'];
						}
					
					}
				}
				
				foreach($numarr as $k => $v){
					$config.=$v.','.$pricearr[$k].',';
				}
				$config=rtrim($config,',');
				$cuxiao=new \App\cuxiao();
				$cuxiao->cuxiao_config=$config;
				$cuxiao->cuxiao_goods_id=$goods_id;
				$cuxiao->cuxiao_msg=$data['cuxiao_msg'];
				$cuxiao->cuxiao_type='2';
				$msg=$cuxiao->save();
				if(!$msg){
						return false;
					}
				return true;
				break;
			case '3':
					\DB::table('cuxiao')->where('cuxiao_goods_id',$goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods_id)->delete();
				$data=$request->all();
				if(isset($data['cuxiao_num'])&&$data['cuxiao_num']!=''&&$data['cuxiao_num']!=null){
						foreach($data['cuxiao_num'] as $key => $val){
						$cuxiao=\App\cuxiao::where('cuxiao_id',$key)->first();
						$cuxiao=new \App\cuxiao();
						$cuxiao->cuxiao_goods_id=$goods_id;
						$cuxiao->cuxiao_type='3';
						$cuxiao->cuxiao_config=$val.','.$data['cuxiao_prize'][$key];
						$cuxiao->cuxiao_msg=$data['cuxiao_msg'][$key];
						if($data['cuxiao_special'][$key]!='0'){
									$special=new \App\special();
									$special->special_type='3';
									$special->special_num=$val;
									$special->special_goods_id=$request->input('goods_id');
									$special->special_price_id=$data['cuxiao_special'][$key];
									$special->special_price_num=1;
									$msg=$special->save();
									$cuxiao->cuxiao_special_id=$special->special_id;
									if(!$msg){
										return false;
									}
						}
						$msg=$cuxiao->save();
						if(!$msg){
							return false;
						}
					}
				
				}
				if(isset($data['new_cuxiao'])&&$data['new_cuxiao']!=''&&$data['new_cuxiao']!=null){
					$new_cuxiao=$data['new_cuxiao'];
					foreach($new_cuxiao as $key => $val){
						if($val['msg']!=''&&$val['msg']!=null&&$val['num']!=''&&$val['num']!=null&&$val['price']!=''&&$val['price']!=null){
							$cuxiao=new \App\cuxiao();
							$cuxiao->cuxiao_msg=$val['msg'];
							$cuxiao->cuxiao_config=$val['num'].','.$val['price'];
							$cuxiao->cuxiao_goods_id=$goods_id;
							$cuxiao->cuxiao_type='3';
							if($val['free']!=0){
								$special=new \App\special();
								$special->special_type='3';
								$special->special_num=$val['num'];
								$special->special_goods_id=$request->input('goods_id');
								$special->special_price_id=$val['free'];
								$special->special_price_num=1;
								$msg=$special->save();
								$cuxiao->cuxiao_special_id=$special->special_id;
								if(!$msg){
									return false;
								}
							}
							$cuxiao->save();
						}
					}
				}
				return true;
				break;
			default:
				break;
		}
	}
	public function saveupdate(Request $request){
		$goods=\App\goods::where('goods_id',$request->input('goods_id'))->first();
		$type=$request->input('goods_cuxiao_type');
		switch($type){
			case '0':
			\DB::table('cuxiao')->where('cuxiao_goods_id',$goods->goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods->goods_id)->delete();
				return true;
				break;
			case '1':
			\DB::table('cuxiao')->where('cuxiao_goods_id',$goods->goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods->goods_id)->delete();
				return true;
				break;
			case '2':
					\DB::table('cuxiao')->where('cuxiao_goods_id',$goods->goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods->goods_id)->delete();
			$data=$request->all();
			$config="";
				$numarr=[];
				$pricearr=[];
				if(isset($data['cuxiao_num'])&&$data['cuxiao_num']!=null){
					foreach ($data['cuxiao_num'] as $key => $value) {
						$numarr[]=$value;
						$pricearr[]=$data['cuxiao_prize'][$key];
						//$config.=$value.','.$data['cuxiao_prize'][$key];
					}
				}
				if(isset($data['new_cuxiao'])&&$data['new_cuxiao']!=null){
					foreach($data['new_cuxiao'] as $key => $v){
						if($v['num']!=''&&$v['price']!=''&&$v['num']!=null&&$v['price']!=''){
								$numarr[]=$v['num'];
								$pricearr[]=$v['price'];
						}
					
					}
				}
				
				foreach($numarr as $k => $v){
					$config.=$v.','.$pricearr[$k].',';
				}
				$config=rtrim($config,',');
				$cuxiao=new \App\cuxiao();
				$cuxiao->cuxiao_config=$config;
				$cuxiao->cuxiao_goods_id=$goods->goods_id;
				$cuxiao->cuxiao_msg=$data['cuxiao_msg'];
				$cuxiao->cuxiao_type='2';
				$msg=$cuxiao->save();
				//\App\order::where('order_goods_id',$goods->goods_id)->update(['order_cuxiao_id'=>$cuxiao->cuxiao_id]);
				if(!$msg){
						return false;
					}
				return true;
				break;
			case '3':
					\DB::table('cuxiao')->where('cuxiao_goods_id',$goods->goods_id)->delete();
					\DB::table('special')->where('special_goods_id',$goods->goods_id)->delete();
				$data=$request->all();
				if(isset($data['cuxiao_num'])&&$data['cuxiao_num']!=''&&$data['cuxiao_num']!=null){
						foreach($data['cuxiao_num'] as $key => $val){
						$cuxiao=\App\cuxiao::where('cuxiao_id',$key)->first();
						$cuxiao=new \App\cuxiao();
						$cuxiao->cuxiao_goods_id=$goods->goods_id;
						$cuxiao->cuxiao_type='3';
						$cuxiao->cuxiao_config=$val.','.$data['cuxiao_prize'][$key];
						$cuxiao->cuxiao_msg=$data['cuxiao_msg'][$key];
						if($data['cuxiao_special'][$key]!='0'){
									$special=new \App\special();
									$special->special_type='3';
									$special->special_num=$val;
									$special->special_goods_id=$request->input('goods_id');
									$special->special_price_id=$data['cuxiao_special'][$key];
									$special->special_price_num=1;
									$msg=$special->save();
									$cuxiao->cuxiao_special_id=$special->special_id;
									if(!$msg){
										return false;
									}
						}
						$msg=$cuxiao->save();
						if(!$msg){
							return false;
						}
					}
				
				}
				if(isset($data['new_cuxiao'])&&$data['new_cuxiao']!=''&&$data['new_cuxiao']!=null){
					$new_cuxiao=$data['new_cuxiao'];
					foreach($new_cuxiao as $key => $val){
						if($val['msg']!=''&&$val['msg']!=null&&$val['num']!=''&&$val['num']!=null&&$val['price']!=''&&$val['price']!=null){
							$cuxiao=new \App\cuxiao();
							$cuxiao->cuxiao_msg=$val['msg'];
							$cuxiao->cuxiao_config=$val['num'].','.$val['price'];
							$cuxiao->cuxiao_goods_id=$goods->goods_id;
							$cuxiao->cuxiao_type='3';
							if($val['free']!=0){
								$special=new \App\special();
								$special->special_type='3';
								$special->special_num=$val['num'];
								$special->special_goods_id=$request->input('goods_id');
								$special->special_price_id=$val['free'];
								$special->special_price_num=1;
								$msg=$special->save();
								$cuxiao->cuxiao_special_id=$special->special_id;
								if(!$msg){
									return false;
								}
							}
							$cuxiao->save();
							//\App\order::where('order_goods_id',$goods->goods_id)->update(['order_cuxiao_id'=>$cuxiao->cuxiao_id]);
						}
					}
				}
				return true;
				break;
			default:
				break;
		}
	}
	public static function check_role(Request $request)
	{
		//验证商品促销信息是否合法
		$data=$request->all();
		$cuxiao_type=$request->goods_cuxiao_type;
		$is_fine=true;
		switch ($cuxiao_type) {
			case '0':
				
				break;
			case '1':
				
				break;
			case '2':
				if(!$request->has('cuxiao_msg')||$request->input('cuxiao_msg')==null){
					$is_fine=false;
					break;
				}
				if(!$request->has('cuxiao_num')&&!$request->has('new_cuxiao')){
					$is_fine=false;
					break;
				}
				$data=$request->all();
				if($request->has('cuxiao_num')){
					if($data['cuxiao_num']==null){
						$is_fine=false;break;
					}
					foreach($data['cuxiao_num'] as $k => $v){
						if($v==null||$data['cuxiao_prize'][$k]==null){
							$is_fine=false;
							break;
						}
					}
				}
				if($request->has('new_cuxiao')){
					if($data['new_cuxiao']==null){
						$is_fine=false;break;
					}
					foreach($data['new_cuxiao'] as $k => $v){
						if($v['num']==null||$v['price']==null){
							$is_fine=false;
							break;
						}
					}
				}
				break;
			case '3':
				if(!$request->has('cuxiao_num')&&!$request->has('new_cuxiao')){
					$is_fine=false;break;
				}
				$data=$request->all();
				if($request->has('cuxiao_num')){
					if($data['cuxiao_num']==null){
						$is_fine=false;break;
					}
					foreach($request->input('cuxiao_num') as $k => $v){
						if($v==null||$data['cuxiao_msg'][$k]==null||$data['cuxiao_prize']==null){
							$is_fine=false;break;
						}
					}
				}
				if($request->has('new_cuxiao')){
					if($data['new_cuxiao']==null){
						$is_fine=false;break;
					}
					foreach($request->input('new_cuxiao') as $k => $v){
						if($v['msg']==null||$v['num']==null||$v['price']==null){
							$is_fine=false;break;
						}
					}
				}
				break;
			default:
				$is_fine=false;
				break;
		}
		return $is_fine;
	}
	public function get_diff_price($attr,$price)
	//计算差额
	{
		foreach($attr as $k =>$v){
			foreach($v as $key => $val){
				$price+=\App\config_val::where('config_val_id',$val)->first()['config_diff_price'];
			}
		}
		return $price;
	}
	public function get_cheap_price($goods_cheap_id,$price)
	{
		$goods=$this->goods;
		$goods_cheap=\App\goods_cheap::where([['goods_cheap_goods_id',$goods->goods_id],['goods_cheap_id',$goods_cheap_id]])->first();
		if($goods_cheap==null||strtotime($goods_cheap->goods_cheap_start_time)>time()){
			return $price;
		}
		switch ($goods_cheap->goods_cheap_type) {
			case '0':
				$old_price=$price;
				if($price-$goods_cheap->goods_cheap_msg>0){
					return $price-$goods_cheap->goods_cheap_msg;
				}else{
					return $old_price;
				}
				break;
			case '1':
				$old_price=$price;
				$price=floor($price*($goods_cheap->goods_cheap_msg / 10));
				$currency_type=\App\currency_type::where('currency_type_id',$goods->goods_currency_id)->first();
				if($currency_type->exchange_rate<0.001){
					if($price<1000){
						return $old_price;
					}
					$price=mb_substr($price, 0, mb_strlen($price) - 3)*1000;
				}
				if($price>0){
					return $price;
				}else{
					return $old_price;
				}
				break;
			case '2':
				$old_price=$price;
				if($price>=$goods_cheap->goods_cheap_remark){
					$price=$price-$goods_cheap->goods_cheap_msg;
					if($price>0){
						return $price;
					}else{
						return $old_price;
					}
				}else{
					return $old_price;
				}
				break;
			default:
				return $price;
				break;
		}
	}
}