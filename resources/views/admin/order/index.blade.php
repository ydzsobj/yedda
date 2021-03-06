@extends('admin.father.css')
@section('content')

<!-- <link rel="stylesheet" type="text/css" href="{{asset('/admin/static/fixedColumns.dataTables.min.css')}}" /> -->
<link rel="stylesheet" type="text/css" href="{{asset('/admin/static/jquery.dataTables.min.css')}}" />
<script type="text/javascript" src="{{asset('/admin/lib/jquery/jquery-3.3.1.js')}}"></script> 
<style>
 #order_index_table_wrapper .dataTables_scroll .dataTables_scrollHead table thead th{
	border-left: none;   
 }
 .paginate_input{
	width: 60px;
    text-align: center;
    border: solid 1px #ddd;
    padding: 5px;
 }
 .back_to_top{
    position: fixed;
    bottom:5%;
    right: 50%;
    border:1px solid #888;
    z-index:1000;
}
</style>
<!-- 上面样式解决dataTable;border-left错开BUG -->
<div class="page-container">
		
		<div class="text-c">
			ID:<input type="text" class="input-text" style="width:120px;margin:30px;" id="search_by_id" > 
		日期范围：
		<input type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss', maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d %H:%m:%s\'}' })" id="datemin" class="input-text Wdate" style="width:120px;">
		-
		<input type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss', minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d %H:%m:%s' })" id="datemax" class="input-text Wdate" style="width:120px;">
		<!-- <input type="text" class="input-text" style="width:250px" placeholder="输入管理员名称" id="" name=""> -->
		<button type="submit" class="btn btn-success" id="seavis1" name=""><i class="Hui-iconfont">&#xe665;</i> 搜记录</button>
		&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-success" style="border-radius: 8%;" id="outorder" name=""><i class="Hui-iconfont">&#xe640;</i> 数据导出</button>
	</div>
	
	<div style="margin:0px 45%;"><br/><a href="javascript:0;" id="getadmin" class="btn btn-primary radius"><i class="icon Hui-iconfont"></i> 筛选</a></div><br/>
	<button class="back_to_top btn">返回顶部</button>
	<div style="display: none" id="select-admin">
		<div class="row cl">
			<label class="form-label col-xs-1 col-sm-1">导出时间类型：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
				<select name="order_time" id="order_time" class="select">
					<option value="0">订单创建时间</option>
					<option value="1">订单核审时间</option>
				</select>
			</span>
			</div>
			@if(Auth::user()->is_root=='1')
				<label class="form-label col-xs-1 col-sm-1">账户名：</label>
				<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
				<select name="admin_name" id="admin_name" class="select">
					<option value="0">所有</option>
					@foreach($admins as $val)
						<option value="{{$val->admin_id}}" >{{$val->admin_name.'('.$val->admin_show_name.')'}}</option>
					@endforeach
				</select>
				</span>
				</div>
			@endif
			<label class="form-label col-xs-1 col-sm-1">订单核审状态：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
				<select name="order_type" id="order_type" class="select">
					<option value="#">所有</option>
					<option value="0">未核审</option>
					<option value="1">通过核审</option>
					<option value="2">拒绝核审</option>
					<option value="3">已发货</option>
					<option value="4">已签收</option>
					<option value="5">退货未退款</option>
					<option value="6">退货并已退款</option>
					<option value="7">未退货已退款</option>
					<option value="8">拒签</option>
					<option value="9">预支付</option>
					<option value="10">取消支付</option>
					<option value="11">支付成功</option>
					<option value="12">支付失败</option>
					<option value="13">支付成功但无paypal数据</option>
					<option value="14">问题订单</option>
				</select>
				</span>
			</div>
		</div>
		<div class="row cl" style="margin-top: 20px;">
			<label class="form-label col-xs-1 col-sm-1">支付方式：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
				<select name="pay_type" id="pay_type" class="select">
					<option value="#">所有</option>
					<option value="0">货到付款</option>
					<option value="1">在线支付</option>
				</select>
			</span>
			</div>
			<label class="form-label col-xs-1 col-sm-1">ip重复：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
					<select name="order_repeat_ip" id="order_repeat_ip" class="select">
						<option value="0">无</option>
						<option value="1">ip</option>
					</select>
					</span>
			</div>
			<label class="form-label col-xs-1 col-sm-1">姓名重复：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
					<select name="order_repeat_name" id="order_repeat_name" class="select">
						<option value="0">无</option>
						<option value="1">姓名</option>
					</select>
					</span>
			</div>
		</div>
		<div class="row cl" style="margin-top: 20px;">
			<label class="form-label col-xs-1 col-sm-1">手机号重复：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
					<select name="order_repeat_tel" id="order_repeat_tel" class="select">
						<option value="0">无</option>
						<option value="1">手机号</option>
					</select>
					</span> </div>
			<label class="form-label col-xs-1 col-sm-1">语种：</label>
			<div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
					@if(Auth::user()->languages == 0)
						<select name="languages" id="languages" class="select">
							<option value="0">所有</option>
							@foreach($languages as $k => $v)
								<option value="{{$k}}">{{$v}}</option>
							@endforeach
						</select>
					@else
						<div><input readonly name="languages" id="languages" style="display: none" value="{{Auth::user()->languages}}" type="text">{{$languages[Auth::user()->languages]}}</div>
					@endif
					</span>
			</div>
			<label class="form-label col-xs-1 col-sm-1">地区：</label>
			<div class="formControls col-xs-2 col-sm-2">
			<span class="select-box">
				<select name="goods_blade_type" id="goods_blade_type" class="select">
					<option value="0">所有</option>
					<option value="1">台湾</option>
					<option value="2">阿联酋</option>
					<option value="3">马来西亚</option>
					<option value="4">泰国</option>
					<option value="5">日本</option>
					<option value="6">印度尼西亚</option>
					<option value="7">菲律宾</option>
					<option value="8">英国</option>
					<option value="9">美国</option>
					<option value="10">越南</option>
					<option value="11">沙特</option>
					<option value="12">卡塔尔</option>
					<option value="12">科威特</option>
				</select>
			</span>
			</div>
		</div>
	</div>

	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="pl_del()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> </span><span class="l"><a href="javascript:;" onclick="order_up('订单批量核审','/admin/order/heshen?type=all','2','800','500')" class="btn btn-secondary radius"><i class="Hui-iconfont">&#xe627;</i> 批量核审</a> </span> <span class="r">共有数据：<strong>{{$counts}}</strong> 条</span><br> </div>
	<table class="table table-border table-bordered table-bg" id="order_index_table">
		<thead>
			<tr>
				<th scope="col" colspan="24" style="white-space: nowrap">订单列表</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" class="allchecked" name="" value="0"></th>
				<th width="40">ID</th>
				<th width="80">订单号</th>
				<th width="60">下单者ip</th>
				<th width="60">单品名</th>
				<th width="60">收货人</th>
				<th width="60">收货电话</th>
				<th width="30">订单价格</th>
				<th width="30">订单状态</th>
				<th width="40">下单时间</th>
				<th width="60">详细地址</th>
				<th width="60">留言</th>
				<th width="30">件数</th>
				<th width="60">快递单号</th>
				<th width="60">促销信息</th>
				<th width="100">属性信息</th>
				<th width="100">sku信息</th>
				<th width="60">收货人邮箱</th>
				<th width="60">收货人地区</th>
				<th width="40">核审时间</th>
				<th width="40">核审者</th>
				<th width="40">邮件通知</th>
				<th width="100">客服备注</th>
				<th width="130">操作</th>
			</tr>
		</thead>
		<tbody>
<!-- 			<tr class="text-c">
				<td><input type="checkbox" value="1" name=""></td>
				<td>1</td>
				<td>admin</td>
				<td>13000000000</td>
				<td>admin@mail.com</td>
				<td>超级管理员</td>
				<td>2014-6-11 11:11:42</td>
				<td class="td-status"><span class="label label-success radius">已启用</span></td>
				<td class="td-manage"><a style="text-decoration:none" onClick="admin_stop(this,'10001')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a> <a title="编辑" href="javascript:;" onclick="admin_edit('管理员编辑','admin-add.html','1','800','500')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> <a title="删除" href="javascript:;" onclick="admin_del(this,'1')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
			</tr> -->
		</tbody>
	</table>
</div>
<!-- <div style="width: 200px;height: 150px;position: absolute;margin-top:20px;z-index: 1000;top:0;right: 0;">
	<div>
		<div style="width: 20px;height: 20px;background-color:#FFE4E1;display: inline-block;"></div>
		<div style="display:inline;">ip重复</div>
	</div>
	<div>
		<div style="width: 20px;height: 20px;background-color:#CAE1FF;display: inline-block;"></div>
		<div style="display:inline;">姓名重复</div>
	</div>
	<div>
		<div style="width: 20px;height: 20px;background-color:#00cc66;display: inline-block;"></div>
		<div style="display:inline;">电话重复</div>
	</div>
	</div>
<div style="width: 200px;height: 150px;position: absolute;margin-top:20px;z-index: 1000;top:0;right: 200px;">
	<div>
		<div style="width: 20px;height: 20px;background-color:#d7dde4;display: inline-block;"></div>
		<div style="display:inline;">ip、姓名</div>
	</div>
	<div>
		<div style="width: 20px;height: 20px;background-color:#ff9900;display: inline-block;"></div>
		<div style="display:inline;">ip、电话重复</div>
	</div>
	<div>
		<div style="width: 20px;height: 20px;background-color:#FFE4C4;display: inline-block;"></div>
		<div style="display:inline;">姓名、电话重复</div>
	</div>
	<div>
		<div style="width: 20px;height: 20px;background-color:#FFFACD;display: inline-block;"></div>
		<div style="display:inline;">ip、姓名、电话重复</div>
	</div>
</div>
</div> -->
@endsection
@section('js')

<script type="text/javascript">
var checkboxs=[];
function shuaxin(){
	
	$("#order_index_table").DataTable().draw(false);
	// quanxuan()
}
function fuxuan(){
	checkboxs=[];
}
function quanxuan(){
	var allLength=$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[name='aaaa']").length; //所有的checkbox的长度
	$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']").each(function(){
            // $(this).on('click',function(){
                var selectedLength=$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[name='aaaa']:checked").length;//所有的选中的checkbox的长度
				if((selectedLength==allLength)&&(selectedLength!=0)){
                    $('.allchecked').prop("checked",true);//全选按钮
                }else{
                        $('.allchecked').prop("checked",false);
                }  
            })
}
$('body').on('click',".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']",function(){
	var allLength=$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']").length; //所有的checkbox的长度
	
	var selectedLength=$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']:checked").length;//所有的选中的checkbox的长度
                if(selectedLength==allLength){
                    $('.allchecked').prop("checked",true);//全选按钮
                    }else{
                        $('.allchecked').prop("checked",false);
                }
            
})
// 复选框选择
function states(){
	$("input[type='checkbox']").each(function() {
				for(var i=0;i<=checkboxs.length;i++){
					if($(this).val()==checkboxs[i]){
						$(this).prop("checked", true);
					}
				}
	});
	// quanxuan()
}

	var backButton=$('.back_to_top');
    function backToTop() {
        $('html,body').animate({
            scrollTop: 0
        }, 800);
    }
    backButton.on('click', backToTop);
 
    $(window).on('scroll', function () {/*当滚动条的垂直位置大于浏览器所能看到的页面的那部分的高度时，回到顶部按钮就显示 */
        if ($(window).scrollTop() > $(window).height()-300)
            backButton.fadeIn();
        else
            backButton.fadeOut();
    });
    $(window).trigger('scroll');/*触发滚动事件，避免刷新的时候显示回到顶部按钮*/
	$.tablesetting={
		"pagingType": "input",
	"lengthMenu": [[10,20,30,40],[10,20,30,40]],//每页显示条数
		"paging": true,					//是否分页。
		"info":   true,					//页脚信息
		"searching": true,				//搜索
		"ordering": true,
		"order": [[ 9, "desc" ]],
		"stateSave": false,
		"columnDefs": [{
		   "targets": [0,1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,19,20,21,22,23],
		   "orderable": false
		}],
		scrollX:        true,
        scrollCollapse: true,
        fixedColumns:   {
            leftColumns: 3,
            rightColumns: 2
        },
		"processing": true,
		"serverSide": true,
		"ajax": {
		"data":{
			goods_search:function(){return $('#admin_name').val()},
			order_repeat_ip:function(){return $('#order_repeat_ip').val()},
			order_repeat_name:function(){return $('#order_repeat_name').val()},
			order_repeat_tel:function(){return $('#order_repeat_tel').val()},
			search_by_id:function(){return $('#search_by_id').val()},
			mintime:function(){return $('#datemin').val()},
			maxtime:function(){return $('#datemax').val()},
			order_type:function(){return $('#order_type').val()},
            pay_type:function(){return $('#pay_type').val()},
            languages:function(){return $('#languages').val()},
            goods_blade_type:function(){return $('#goods_blade_type').val()},
            order_time:function (){return $('#order_time').val()},
		},
		"url": "{{url('admin/order/get_table')}}",
		"type": "POST",
		'headers': { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
		},
		"columns": [
		{'defaultContent':"","className":"td-manager"},
		{"data":'order_id'},
		{"data":'order_single_id'},
		{"data":'order_ip'},
		{'data':'goods_real_name'},
		{'data':'order_name'},
		{'data':'order_tel'},
		{'data':'order_price'},
		{'defaultContent':"","className":"td-manager"},
		{'data':'order_time'},
		{'data':'order_add'},
		{'data':'order_remark'},
		{'data':'order_num'},
		{'data':'order_send'},
		{'data':'order_cuxiao_id'},
		{'data':'config_msg'},
		{'data':'goods_sku'},
		{'data':'order_email'},
		{'defaultContent':"","className":"td-manager"},
		{'data':'order_return_time'},
		{'data':'admin_show_name'},
		{'defaultContent':"","className":"td-manager"},
        {'data':'order_service_remarks'},
        {'defaultContent':"","className":"td-manager"},
/*		{'data':'course.profession.pro_name'},
		{'defaultContent':""},
		{'defaultContent':""},
		{'data':'created_at'},
		{'defaultContent':"","className":"td-manager"},*/
		],
        //每行回调函数
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            //改行满足的条件
			if(aData.order_repeat_field){
				if(aData.order_repeat_field.length == 1 && aData.order_repeat_field[0] == '1'){
                        //设置满足条件行的背景颜色,ip
                        //$(nRow).css("background", "#FFE4E1");
                        $(nRow).find('td:eq(3)').css('color',"#FF69B4");
                        $(nRow).find('td:eq(3)').css('font-weight',"bold");
						
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");

				}
                if(aData.order_repeat_field.length == 1 && aData.order_repeat_field[0] == '2'){
                    	//     //设置满足条件行的背景颜色,姓名
                        //$(nRow).css("background", "#CAE1FF");
						

                        $(nRow).find('td:eq(5)').css('color',"#FF69B4");
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");
                        $(nRow).find('td:eq(5)').css('font-weight',"bold");
                }
                if(aData.order_repeat_field.length == 1 && aData.order_repeat_field[0] == '3'){
					
                    //     //设置满足条件行的背景颜色,电话
                        //$(nRow).css("background", "#00cc66");
                        $(nRow).find('td:eq(6)').css('color',"#FF69B4");
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");
                        $(nRow).find('td:eq(6)').css('font-weight',"bold");
                }
                if(aData.order_repeat_field.length == 3){
					
                    //     //设置满足条件行的背景颜色
                   // $(nRow).css("background", "#FFFACD");
                    $('.dataTable td.sorting_1').removeClass('sorting_1');
                        $(nRow).find('td:eq(3)').css('color',"#FF69B4");
                        $(nRow).find('td:eq(3)').css('font-weight',"bold");
                        $(nRow).find('td:eq(5)').css('color',"#FF69B4");
                        $(nRow).find('td:eq(5)').css('font-weight',"bold");
                        $(nRow).find('td:eq(6)').css('color',"#FF69B4");
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");
                        $(nRow).find('td:eq(6)').css('font-weight',"bold");
                }
                if(aData.order_repeat_field.length == 2 && aData.order_repeat_field.indexOf('1')>=0 &&  aData.order_repeat_field.indexOf('2')>=0){
                    //     //设置满足条件行的背景颜色
					
                    //$(nRow).css("background", "#d7dde4");
                        $(nRow).find('td:eq(3)').css('color',"#FF69B4");
                        $(nRow).find('td:eq(3)').css('font-weight',"bold");
                        $(nRow).find('td:eq(5)').css('color',"#FF69B4");
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");
                        $(nRow).find('td:eq(5)').css('font-weight',"bold");

                }
                if(aData.order_repeat_field.length == 2 && aData.order_repeat_field.indexOf('1')>=0 &&  aData.order_repeat_field.indexOf('3')>=0){
                    //     //设置满足条件行的背景颜色
					
                    //$(nRow).css("background", "#ff9900");
                    $(nRow).find('td:eq(3)').css('color',"#FF69B4");
                    $(nRow).find('td:eq(3)').css('font-weight',"bold");
                    $(nRow).find('td:eq(6)').css('color',"#FF69B4");
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");
                    $(nRow).find('td:eq(6)').css('font-weight',"bold");
                }
/*                console.log("======================");
                console.log(aData.order_repeat_field.length);
                console.log(aData.order_repeat_field.indexOf('3'));
                console.log(aData.order_repeat_field.indexOf('2'));
                console.log(aData.order_repeat_field);
                console.log("=======================");*/
                if(aData.order_repeat_field.length == 2 && aData.order_repeat_field.indexOf('2')>=0 &&  aData.order_repeat_field.indexOf('3')>=0){
                    //     //设置满足条件行的背景颜色
					
                    //$(nRow).css("background", "#FFE4C4");
                    $(nRow).find('td:eq(5)').css('color',"#FF69B4");
						$(nRow).find('td:eq(0)').find('input:eq(0)').attr("name","repeat");
                    $(nRow).find('td:eq(5)').css('font-weight',"bold");
                    $(nRow).find('td:eq(6)').css('color',"#FF69B4");
                    $(nRow).find('td:eq(6)').css('font-weight',"bold");
                }
			}
        },
        "createdRow":function(row,data,dataIndex){
			var info='<a title="地址" href="javascript:;" onclick="goods_getaddr(\'收货地址\',\'/admin/order/getaddr?id='+data.order_id+'\',\'2\',\'800\',\'500\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="收货地址"><i class="Hui-iconfont">&#xe643;</span></i></a><a title="修改订单" href="javascript:;" onclick="order_edit(\'修改订单\',\'/admin/order/edit?id='+data.order_id+'\',\'2\',\'1200\',\'800\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="修改订单"><i class="Hui-iconfont">&#xe61d;</span></i></a>';
				if(data.order_type<3||data.order_type>8){
					info+='<a title="更改状态" href="javascript:;" onclick="goods_edit(\'更改状态\',\'/admin/order/heshen?id='+data.order_id+'\',\'2\',\'800\',\'500\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="更改状态"><i class="Hui-iconfont">&#xe6df;</i></span></a>';
				}
			info+='<a title="短信推送" href="javascript:;" onclick="send_message(\'短信推送\',\'/admin/order/send_message?id='+data.order_id+'\',\'2\',\'800\',\'500\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="短信推送"><i class="Hui-iconfont">&#xe61f;</i></span></a><a title="短信记录" href="javascript:;" onclick="message_logs(\'短信记录\',\'/admin/order/message_logs?id='+data.order_id+ '\',\'2\',\'800\',\'500\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="短信记录"><i class="Hui-iconfont">&#xe64f;</i></span></a><a title="删除" href="javascript:;" onclick="del_order(\''+data.order_id+'\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="删除"><i class="Hui-iconfont">&#xe609;</i></span></a><a title="客服备注" href="javascript:;" onclick="order_edit(\'客服备注\',\'/admin/order/remarks?id='+data.order_id+'\',\'2\',\'500\',\'400\')" class="ml-5" style="text-decoration:none"><span class="btn btn-primary" title="客服备注"><i class="Hui-iconfont">&#xe692;</i></span></a>';
			if(data.order_type==0){
				var isroot='<a href="#" onclick="" <span class="label label-success radius" style="color:#ccc;">未核审</span></a>';
			}else if(data.order_type==1){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:green;">核审通过</span></a>';
			}else if(data.order_type==2){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:red;">核审驳回</span></a>';
			}else if(data.order_type==3){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:brown;">已扣货</span></a>';
			}else if(data.order_type==4){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:#6699ff;">已出仓</span></a>';
			}else if(data.order_type==5){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:#red;">供应驳回</span></a>';
			}else if(data.order_type==6){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:#red;">退货并已退款</span></a>';
			}else if(data.order_type==7){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:#red;">未退货并已退款</span></a>';
			}else if(data.order_type==8){
				var isroot='<a href="javascript:;" onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:#red;">拒签</span></a>';
			}else if(data.order_type==9){
				var isroot='<a href="javascript:;"  <span class="label label-default radius" style="color:black;background-color:#ccc;">预支付</span></a>';
			}else if(data.order_type==10){
				var isroot='<a href="javascript:;"  <span class="label label-default radius" style="color:black;background-color:#ccc;">取消支付</span></a>';
			}else if(data.order_type==11){
				var isroot='<a href="javascript:;"  <span class="label label-default radius" style="color:black;background-color:#ccc;">支付成功</span></a>';
			}else if(data.order_type==12){
				var isroot='<a href="javascript:;"  <span class="label label-default radius" style="color:black;background-color:#ccc;">支付失败</span></a>';
			}else if(data.order_type==13){
				var isroot='<a href="javascript:;"  <span class="label label-default radius" style="color:black;background-color:#ccc;">支付成功但无paypal数据</span></a>';
			}else if(data.order_type==14){
				var isroot='<a href="javascript:;" 	onclick="order_returninfo('+data.order_id+')" <span class="label label-default radius" style="color:red;">问题订单</span></a>';
			}
			if(data.order_pay_type.indexOf("1")!=-1){
				isroot+='<a href="javascript:;" onclick="order_payinfo('+data.order_id+')" <span class="label label-default radius" style="color:black;background-color:white;">支付信息</span></a>';
			}
			if(data.order_isemail==0){
				var emailsend='未发送<input class="btn btn-secondary-outline radius" onclick="send_mail('+data.order_id+')" type="button" value="补发">';
			}else if(data.order_isemail==1){
				var emailsend='<span style="color:green;">发送成功</span>';
			}else{
				var emailsend='<span style="color:red;">发送失败</span><input class="btn btn-secondary-outline radius" onclick="send_mail('+data.order_id+')" type="button" value="补发">';
			}
			var checkbox='<input type="checkbox" name="aaaa" value="'+data.order_id+'">';
			$(row).find('td:eq(0)').html(checkbox);
			/*var info='<a title="编辑" href="javascript:;" onclick="member_edit(\'编辑\',\'member-add.html\',4,\'\',510)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> <a title="删除" href="javascript:;" onclick="member_del(this,1)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>';*/
			$(row).find('td:eq(23)').html(info);
			$(row).find('td:eq(21)').html(emailsend);
			$(row).find('td:eq(8)').html(isroot);
			$(row).find('td:eq(18)').html(data.order_state+'-'+data.order_city);
			/*$(row).find('td:eq(0)').html(checkbox);*/
			$(row).addClass('text-c');
			/*var img="<img src='"+data.cover_img+"' alt='暂时没有图片' width='130' height='100'>";
			$(row).find('td:eq(5)').html(img);*/
			/*var video_btn='<input class="btn btn-success-outline radius" onClick="start_play('+data.lesson_id+')" type="button" value="播放视频">';
			$(row).find('td:eq(6)').html(video_btn);*/
		},
		"fnDrawCallback": function () {
			states();
		},
		"fnPreDrawCallback":function(){
			setTimeout(function(){ quanxuan(); }, 500);
		}
	}
 dataTable =$('#order_index_table').DataTable($.tablesetting);
$('#seavis1').on('click',function(){
	$('#order_index_table').dataTable().fnClearTable(); 

})
function del_order(id){
		var msg =confirm("确定要删除此订单吗？");
		if(msg){
        		layer.msg('删除中');
        			$.ajax({
					url:"{{url('admin/order/delorder')}}",
					type:'get',
					data:{'id':id},
					datatype:'json',
					success:function(msg){
			           if(msg['err']==1){
			           	 layer.msg(msg.str);
			           	 /*$(".del"+id).prev("input").remove();
        				 $(".del"+id).val('已删除');*/
        				 /*dataTable.fnDestroy(false);
               			 dataTable = $("#goods_index_table").dataTable($.tablesetting);*/
               			 //搜索后跳转到第一页
               			 //dataTable.fnPageChange(0);
               			 $('#order_index_table').dataTable().fnClearTable(); 
			           }else if(msg['err']==0){
			           	 layer.msg(msg.str);
			           }else{
			           	 layer.msg('删除失败！');
			           }
					}
				})
        	}else{
                
        	}
	}
function order_returninfo(id){
	layer_show('订单信息','/admin/order/orderinfo?id='+id,500,300);
}
function order_payinfo(id){
	layer_show('订单信息','/admin/order/payinfo?id='+id,700,400);
}
function goods_edit(title,url,type,w,h){
	layer_show(title,url,w,h);
}
function send_message(title,url,type,w,h) {
	layer_show(title, url, w, h);
}
function message_logs(title,url,type,w,h) {
	layer_show(title, url, w, h)
}
function order_up(title,url,type,w,h){
	xuanzhe()
	var b='';
	var a=$('input[type="checkbox"]:checked');
	if(checkboxs.length==0){
		layer.msg('无选中项');
		return false;
	}
	// for (var i = a.length - 1; i >= 0; i--) {
	// 	if(a[i].value!=''&&a[i].value!=null){
	// 				b+=a[i].value+',';
	// 				checkboxs.push(a[i].value)
	// 	}
	// }
	url=url+'&id='+checkboxs;
	layer_show(title,url,w,h);
}
$('#outorder').on('click',function(){
	var url='{{url("admin/order/outorder")}}'+'?';
	var is_time=false;
	//日期参数
	var mintime=$('#datemin').val();
	var maxtime=$('#datemax').val();
	if(mintime==''&&maxtime==''){
/*		layer.msg('请稍等');
     location.href='{{url("admin/order/outorder")}}'+'?';*/
	}else if(mintime==''||maxtime==''){
/*		layer.msg('请选择正确日期区间');
*/	}else{
		is_time=true;
/*		layer.msg('请稍等');
		location.href='{{url("admin/order/outorder")}}?min='+mintime+'&max='+maxtime;*/
		url+='min='+mintime+'&max='+maxtime;
	}
	//账户参数
	var admin_name=$('#admin_name').val();
	if(admin_name>=0){
		if(is_time){
			url+='&admin_name='+admin_name;
		}else{
			url+='admin_name='+admin_name;
		}
	}else{
		if(is_time){
			url+='&admin_name=0';
		}else{
			url+='admin_name=0';
		}
	}
	//订单状态参数
	var order_type=$('#order_type').val();
	if(order_type>=0){
		url+='&order_type='+order_type;
	}else{
		url+='&order_type=null';
	}
	//支付类型参数
	var pay_type=$('#pay_type').val();
	if(pay_type>=0){
		url+='&pay_type='+pay_type;
	}else{
		url+='&pay_type=null';
	}
	//语言参数
	var languages=$('#languages').val();
	if(languages>=0){
		url+='&languages='+languages;
	}else{
		url+='&languages=0';
	}
	//地区参数
	var goods_blade_type=$('#goods_blade_type').val();
	if(goods_blade_type>=0){
		url+='&goods_blade_type='+goods_blade_type;
	}else{
		url+='&goods_blade_type=0';
	}

	var order_time=$('#order_time').val();
    url+='&order_time='+order_time;
    layer.msg('请稍等');
	location.href=url;
})
function pl_del(){
	xuanzhe()
	var msg =confirm("确定要批量删除这些订单吗？");
	if(!msg){
		return false;
	}
	var b=[];
	var a=$('input[type="checkbox"]:checked');
	if(checkboxs.length==0){
		layer.msg('无选中项');
		return false;
	}
	// for (var i = a.length - 1; i >= 0; i--) {
	// 	if(a[i].value!=''&&a[i].value!=null){
	// 				// b.push(a[i].value);
	// 				checkboxs.push(a[i].value)
	// 	}
	// }
	layer.msg('删除中，请稍等!'); 
	$.ajax({
					url:"{{url('admin/order/delorder')}}",
					type:'get',
					data:{'id':checkboxs,'type':'all'},
					datatype:'json',
					success:function(msg){
			           if(msg['err']==1){
			           	 layer.msg(msg.str);
               			 $('#order_index_table').dataTable().fnClearTable(); 
			           }else if(msg['err']==0){
			           	 layer.msg(msg.str);
			           }else{
			           	 layer.msg('删除失败！');
			           }
					}
				})


}
 

function pl_update(){
	var msg =confirm("确定要批量核审这些订单吗？");
	if(!msg){
		return false;
	}
	var b=[];
	var a=$('input[type="checkbox"]:checked');
	if(a.length<=0){
		layer.msg('无选中项');
		return false;
	}
	for (var i = a.length - 1; i >= 0; i--) {
		if(a[i].value!=null){
					b.push(a[i].value);
		}
	}
	layer.msg('核审中，请稍等!');
	$.ajax({
					url:"{{url('admin/order/heshen')}}",
					type:'get',
					data:{'id':b,'type':'all'},
					datatype:'json',
					success:function(msg){
			           if(msg['err']==1){
			           	 layer.msg(msg.str);
               			 $('#order_index_table').dataTable().fnClearTable(); 
			           }else if(msg['err']==0){
			           	 layer.msg(msg.str);
			           }else{
			           	 layer.msg('核审失败！');
			           }
					}
				})


}
function goods_getaddr(title,url,type,w,h){
	layer_show(title,url,w,h);
}
function order_edit(title,url,type,w,h){
    layer_show(title,url,w,h)
}
$('#getadmin').on('click',function(){
	$('#select-admin').toggle(300);
})
$('#admin_name').on('change',function(){
	dataTable.ajax.reload();
	var args = dataTable.ajax.params();
});
$('#order_type').on('change',function(){
	dataTable.ajax.reload();
});
$('#pay_type').on('change',function(){
	dataTable.ajax.reload();
});
//根据语言搜索
$('#languages').on('change',function(){
	dataTable.ajax.reload();
});
//根据地区搜索
$('#goods_blade_type').on('change',function(){
	dataTable.ajax.reload();
});
$('#order_repeat_ip').on('change',function(){
	dataTable.ajax.reload();
})
$('#order_repeat_name').on('change',function(){
	dataTable.ajax.reload();
})
$('#order_repeat_tel').on('change',function(){
	dataTable.ajax.reload();
});

var allcheckedflag=true;
$("body").on("click",".allchecked",function(){
    if(allcheckedflag){
		$("div.DTFC_LeftWrapper input[name='aaaa']").prop("checked", true);
		// $("div.DTFC_LeftWrapper .repeat").prop("checked", false);
		allcheckedflag=false;
	}else{
		$("div.DTFC_LeftWrapper :checkbox").prop("checked", false);
		allcheckedflag=true;
	}
	
})
function xuanzhe(){
	// console.log(checkboxs)
	$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']").each(function(j) {	
			if(!this.checked){
				var a=checkboxs.indexOf( $(this).val() )
				if(a>=0){
					checkboxs.splice( a, 1 );
				}
			}
		});
	$(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']").each(function(j) {
		
		if(this.checked){
			var a=checkboxs.indexOf( $(this).val() )
				if(a<0){
					console.log(checkboxs)
					console.log($(this).val())
					checkboxs.push($(this).val());
				}
			
		}

	});
}
//邮件补发
function send_mail(id){
	layer_show('邮件补发','/admin/order/send_mail?id='+id,500,200);
}
// 上一页
	$('body').on('click','#order_index_table_previous',function(){
		// $("input[type='checkbox']:checked").each(function(j) {
		// 	if (j >= 0) {
		// 		checkboxs.push($(this).val());
		// 	}
		// });
		xuanzhe()
	});
	// 下一页
	$('body').on('click','#order_index_table_next',function(){
		// $(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']").each(function(j) {	
		// 	if(!this.checked){
		// 		checkboxs.splice( checkboxs.indexOf( $(this).val() ), 1 );
		// 	}
		// });
		// $(".DTFC_LeftBodyWrapper .table>tbody>.text-c>.td-manager>input[type='checkbox']").each(function(j) {
			
		// 	if(this.checked){
		// 		checkboxs.push($(this).val());
		// 	}
		// });
		xuanzhe()
	})
	// 第一页
	$('body').on('click','#order_index_table_first',function(){
		// $("input[type='checkbox']:checked").each(function(j) {
		// 	if (j >= 0) {
		// 		checkboxs.push($(this).val());
		// 	}

		// });
		xuanzhe()
	});
	// 最后一页
	$('body').on('click','#order_index_table_last',function(){
		// $("input[type='checkbox']:checked").each(function(j) {
		// 	if (j >= 0) {
		// 		checkboxs.push($(this).val());
		// 	}

		// });
		xuanzhe()
	});
	// 自定义页
	$('body').on('input propertychange','.paginate_input',function(){
		// $("input[type='checkbox']:checked").each(function(j) {
		// 	if (j >= 0) {
		// 		checkboxs.push($(this).val());
		// 	}

		// });
		xuanzhe();
	})
</script>

@endsection