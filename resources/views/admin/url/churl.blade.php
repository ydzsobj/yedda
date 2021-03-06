@extends('admin.father.css')
@section('content')
<style>
	/* 搜索下拉框 */
.box{
    position: absolute;
    top: 36px;
    z-index: 999;
    width: 100%;
	overflow-y: auto;
    padding-right: 30px;
    box-sizing: border-box;
	height: 134px;
	background-color: #fff;
	border: 1px solid #ddd;
}
.box ul{
    border: 1px solid #ddd;
    height: 132px;
    background-color: #fff;
}
.box li{
    padding: 0 15px;
    cursor:pointer;
	z-index: 9999;
}
.box li:first-child{
	color:red;
}
.box li:nth-child(odd){background:#F4F4F4;}
</style>
<article class="page-container">
	<form class="form form-horizontal" id="form-url-chn"><input type="hidden" name="url_id" value="{{$url->url_id}}">
		
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>域名：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$url->url_url}}" placeholder="" id="url_url" name="url_url">
			</div>
		</div>
		
	<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>状态：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<select name="url_type" id="url_type" class="select">
				<option value="0" @if($url!=null && $url->url_type==0)selected='selected'@endif>关闭</option>
				<option value="1" @if($url!=null && $url->url_type==1)selected='selected'@endif>开启</option>
				</select>
				</span>
			 </div>
	</div>
	<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>遮罩等级：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<select name="url_zz_level" id="url_zz_level" class="select">
				<option value="0" @if($url!=null && $url->url_zz_level==0)selected='selected'@endif>全部放行</option>
				<option value="1" @if($url!=null && $url->url_zz_level==1)selected='selected'@endif>屏蔽美国fb人员</option>
				<option value="2" @if($url!=null && $url->url_zz_level==2)selected='selected'@endif>屏蔽所有fb人员</option>
				<option value="3" @if($url!=null && $url->url_zz_level==3)selected='selected'@endif>屏蔽除台湾外所有人员</option>
				<option value="5" @if($url!=null && $url->url_zz_level==5)selected='selected'@endif>屏蔽除泰国外所有人员</option>
				<option value="6" @if($url!=null && $url->url_zz_level==6)selected='selected'@endif>屏蔽除菲律宾外所有人员</option>
				<option value="7" @if($url!=null && $url->url_zz_level==7)selected='selected'@endif>屏蔽除日本外所有人员</option>
				<option value="8" @if($url!=null && $url->url_zz_level==8)selected='selected'@endif>屏蔽除马来西亚外所有人员</option>
				<option value="9" @if($url!=null && $url->url_zz_level==9)selected='selected'@endif>屏蔽除英国外所有人员</option>
				<option value="10" @if($url!=null && $url->url_zz_level==10)selected='selected'@endif>屏蔽除印度尼西亚外所有人员</option>
				<option value="11" @if($url!=null && $url->url_zz_level==11)selected='selected'@endif>屏蔽除阿联酋外所有人员</option>
				<option value="4" @if($url!=null && $url->url_zz_level==4)selected='selected'@endif>屏蔽所有人员</option>
				</select>
				</span>
			 </div>
	</div>
	<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>遮罩导向：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<select name="url_zz_for" id="url_zz_for" class="select">
				<option value="0" @if($url!=null && $url->url_zz_for==0)selected='selected'@endif>遮罩页面</option>
				<option value="1" @if($url!=null && $url->url_zz_for==1)selected='selected'@endif>屏蔽页面</option>
				<option value="2" @if($url!=null && $url->url_zz_for==2)selected='selected'@endif>无法访问</option>
				</select>
				</span>
			 </div>
	</div>
	<div class="row cl"style="position: relative;">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>正常单品：</label>
			<div class="formControls col-xs-8 col-sm-9"> 
			<input type="text" class="input-text chanpin" placeholder=""autocomplete="off" id="goods_kind_name"  oninput="xiala()" name="goods_kind_name" value="">
			<input type="text" style="display: none;" class="input-text chanpin"autocomplete="off" id="url_goods_id" name="url_goods_id" value="">
			<div class="box zhengchang" style="display: none;">
						<ul>
						</ul>
			</div>
			</div>
			 
	</div>
	<div class="row cl"style="position: relative;">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>遮罩单品：</label>
			<div class="formControls col-xs-8 col-sm-9"> 
			<input type="text" class="input-text chanpin1" placeholder=""autocomplete="off" id="goods_kind_name"  oninput="xiala1()" name="goods_kind_name" value="">
			<input type="text" style="display: none;" class="input-text chanpin1"autocomplete="off" id="url_zz_goods_id" name="url_zz_goods_id" value="">
			<div class="box zhezhao" style="display: none;">
						<ul>
						</ul>
			</div>
			</div>
			 
	</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">绑定站点：<span style="color:red;">(绑定站点后将自动解绑与该域名相关的单品页)</span></label>
				<div class="formControls col-xs-8 col-sm-9"><span class="select-box">
				<select name="url_site_id" id="url_site_id" class="select">
					<option value="0"><span style="color:red;">无</span></option>
					@foreach(\App\site::where('status',0)->get() as $k => $v)
						<option @if($url->url_site_id==$v->sites_id) selected @endif value="{{$v->sites_id}}">{{$v->sites_name}}({{\App\admin::getBladeName($v->sites_blade_type)}})</option>
					@endforeach
				</select>
					</span>
				</div>
			</div>
	</div>
	<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">关联广告账户：</label>
			<div class="formControls col-xs-8 col-sm-9 ">
				<div class="check-box">
					@foreach($ad_account as $k => $v)
						<input type="checkbox" id="ad_account" name="ad_account[]"  @if($v->is_belong) checked='checked' @endif value="{{$v->ad_account_id}}"  >
						<label for="checkbox-pinglun">{{$v->ad_account_name}}(@if($v->ad_account_belong=='0') fb @elseif($v->ad_account_belong=='1') Yahoo @else Google @endif)</label><br>
					@endforeach
				</div>
			</div>
	</div>
	<!-- <br>
	<div class="row cl" style="border: 1px solid #ccc;width: 80%;margin:0px auto;">
		<label class="form-label col-xs-4 col-sm-3">正常单品：</label>
		  <div class ="radio-box">  <label class="" style="color:red;"><input type="radio" value="null" name="url_goods_id" id="url_goods_id" class="valid" />无</label>
		  	@if(Auth::user()->is_root=='1')
		  	@foreach(\App\goods::where('is_del','0')->get() as $key => $v)
		    <label class=""><input type="radio" value="{{$v->goods_id}}" name="url_goods_id" id="url_goods_id" class="valid" @if($url->url_goods_id==$v->goods_id) checked="checked" @endif />{{$v->goods_real_name}}</label>
	  		@endforeach
		  	@else
		    @foreach(\App\goods::where('is_del','0')
		      ->where(function($query){
		        $query->whereIn('goods_admin_id',\App\admin::get_group_ids(Auth::user()->admin_id));
		      })->get() as $key => $v)
		    <label class=""><input type="radio" value="{{$v->goods_id}}" name="url_goods_id" id="url_goods_id" class="valid" @if($url->url_goods_id==$v->goods_id) checked="checked" @endif />{{$v->goods_real_name}}</label>
		    @endforeach
		    @endif
		  </div> 
  	</div>
  	<br>
  	<div class="row cl" style="border: 1px solid #ccc;width: 80%;margin:0px auto;">
  		<label class="form-label col-xs-4 col-sm-3">遮罩单品：</label>
  		<div class =“radio-box”>  <label class="" style="color:red;"><input type="radio" value="null" name="url_zz_goods_id" id="url_zz_goods_id" class="valid" />无</label>
  		@if(Auth::user()->is_root=='1')
	 	@foreach(\App\goods::where('is_del','0')->get() as $key => $v)
	    	<label class=""><input type="radio" value="{{$v->goods_id}}" name="url_zz_goods_id" id="url_zz_goods_id" class="valid" @if($url->url_zz_goods_id==$v->goods_id) checked="checked" @endif />{{$v->goods_real_name}}</label>
	    @endforeach
	    @else
		@foreach(\App\goods::where('is_del','0')
	      ->where(function($query){
	        $query->whereIn('goods_admin_id',\App\admin::get_group_ids(Auth::user()->admin_id));
	      })->get() as $key => $v)
	    	<label class=""><input type="radio" value="{{$v->goods_id}}" name="url_zz_goods_id" id="url_zz_goods_id" class="valid" @if($url->url_zz_goods_id==$v->goods_id) checked="checked" @endif />{{$v->goods_real_name}}</label>
	  	@endforeach
	  	@endif
		</div> 
  	</div> -->
	{{csrf_field()}}
	
	<div class="row cl">
		<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
			<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
		</div>
	</div>
	</form>
</article>
@endsection
@section('js')
<script type="text/javascript">
	$(function(){
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
	
	$("#form-url-chn").validate({
		rules:{
			url_url:{
				required:true,
			},
		},
		onkeyup:false,
		focusCleanup:true,
		success:"valid",
		submitHandler:function(form){
			if($('#url_site_id').val()!=0){
					var msg =confirm("绑定站点后将自动解绑与该域名相关的产品页，确定？");
					if(!msg){
						return false;
					}
			}
			$(form).ajaxSubmit({
				type: 'post',
				url: "{{url('admin/url/ajaxup')}}" ,
				success: function(data){
					if(data.err==1){
						layer.msg('配置成功!',{icon:1,time:1000},function(){
							index = parent.layer.getFrameIndex(window.name);
								setTimeout("parent.layer.close(index);",2000);
	                        	window.parent.location.reload();
						});
					 }else{
					layer.msg(data.str,{icon:2,time:1000});
					 }
				},
                error: function(XmlHttpRequest, textStatus, errorThrown){
					layer.msg('error!',{icon:2,time:1000});
				}
			});
			/*var index = parent.layer.getFrameIndex(window.name);
			parent.$('.btn-refresh').click();
			parent.layer.close(index);*/
		}
	});
});

function ajax(type,msg,defaults){
	$.ajax({
			//请求方式
			type:'GET',
			url:'{{url("admin/url/url_goods_ajax")}}?url_id={{$url->url_id}}&type='+type+'&msg='+msg,
			dataType:'json',
			data:{},
			success:function(data){


				// console.log(data);
				var str='<li data-id="null">无</li>';
				var wu=false;
				var wu1=false;
				jQuery.each(data.data,function(key,value){ 
					// console.log(value)

					str+='<li data-id="'+value.goods_id+'">'+value.goods_real_name+'</li>' 
					
					
					if(defaults==true&&value.is_check==true&&type==1){
						$('.chanpin').val(value.goods_real_name);
						$('#url_goods_id').val(value.goods_id);
						wu=true;
					}
					if(defaults==true&&value.is_check==true&&type==2){
						$('.chanpin1').val(value.goods_real_name);
						$('#url_zz_goods_id').val(value.goods_id);
						wu1=true;

					}
				}) 
				if(defaults){
					if(!wu&&type==1){
					$('.chanpin').val('无');

					$('#url_goods_id').val(null);
					$('.chanpin').css("color","red");
				}
				if(!wu1&&type==2){
					$('.chanpin1').css("color","red");
					$('.chanpin1').val('无');
					$('#url_zz_goods_id').val(null);
				}
				}
				if(type==1){
					$('.zhengchang ul').html(str);
				}else{
					$('.zhezhao ul').html(str);
				}
			},
			error:function(jqXHR){

			}
		});
}
ajax(1,false,true);
ajax(2,false,true);
// 正常单品搜索下拉框
$(".chanpin").focus(function(){

		$('.zhengchang').show(400);
		var a=$('.chanpin').val();
		if(a==''){
			a=false;
		}
		ajax(1,a);
		if(a=="无"){
			$('.chanpin').css("color","red");
		}else{
			$('.chanpin').css("color","#000");
		}
});
	function xiala(){
		$('#url_goods_id').val('');
		$('.zhengchang ul').empty();
		$('.zhengchang').show(400);
		var a=$('.chanpin').val();
		if(a==''){
			a=false;
		}
		if(a==="无"){
			$('.chanpin').css("color","red");
		}else{
			$('.chanpin').css("color","#000");
		}
		console.log(a==="无")
		ajax(1,a);
		
		
	}
	

	
	$('body').on('mousedown','.zhengchang li',function(){

		$('.zhengchang').hide(400);
		var content=$(this).text();
		var content_id=$(this).attr('data-id');
		$('.chanpin').val(content);
		$('#url_goods_id').val(content_id);
		$('.zhengchang ul').empty();

		if(content=="无"){
			$('.chanpin').css("color","red");
		}else{
			$('.chanpin').css("color","#000");
		}
	});
    $('.chanpin').on('blur',function(){
        $('.zhengchang').hide(400);
    });



	//遮罩单品
	$(".chanpin1").focus(function(){

		$('.zhezhao').show(400);
		var a=$('.chanpin1').val();
		if(a==''){
		a=false;
		}
		ajax(2,a,false);
	});
	function xiala1(){
		$('#url_zz_goods_id').val('');
		$('.zhezhao ul').empty();
		$('.zhezhao').show(400);
		var a=$('.chanpin1').val();
		if(a==''){
		a=false;
		}
		ajax(2,a,false);

	}



	$('body').on('mousedown','.zhezhao li',function(){
		$('.zhezhao').hide(400);
		var content=$(this).text();
		var content_id=$(this).attr('data-id');
		$('.chanpin1').val(content);
		$('#url_zz_goods_id').val(content_id);
		$('.zhezhao ul').empty();
		if(content=="无"){
			$('.chanpin1').css("color","red");
		}else{
			$('.chanpin1').css("color","#000");
		}
	});
    $('.chanpin1').on('blur',function(){
        $('.zhezhao').hide(400);
    });
    $('#url_site_id').on('change',function(){
    	if($(this).val()!=0){
    		$('.chanpin').css("color","red");
    		$('.chanpin1').css("color","red");
    		$('.chanpin1').val('无');
    		$('.chanpin').val('无');
    		$('#url_zz_goods_id').val(null);
    		$('#url_goods_id').val(null);
    	}
    })
</script>
@endsection