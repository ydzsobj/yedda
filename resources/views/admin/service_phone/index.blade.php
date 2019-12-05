@extends('storage.father.static')
@section('content')
    <style>
        .laytable-cell-1-0-3, .laytable-cell-1-0-5, .laytable-cell-1-0-8, .laytable-cell-1-0-10 { /*最后的pic为字段的field*/
            height: 100%;
            max-width: 100%;
        }
    </style>



    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-header">电话列表</div>
                    <div class="layui-row">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                            <div class="layui-form-item">
                                <div class="layui-inline test-table-reload-btn">
                                    <button class="layui-btn" id="addgoods_kind">新增电话</button>
                                </div>
                            </div>
                           
                        </div>
                        <div class="layui-card-body">
                            <table class="" id="test-table-operate" lay-filter="test-table-operates"></table>
                            <script type="text/html" id="test-table-operate-barDemo">
                                <a class="layui-btn layui-btn-xs" title="修改" lay-event="edit"><i class="layui-icon">&#xe642;</i></a>
                                <a class="layui-btn layui-btn-danger layui-btn-xs" title="删除" lay-event="del"><i
                                    class="layui-icon">&#xe640;</i></a>
                               
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        var that = this;
        layui.config({
            base: '/admin/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table','laytpl','laydate'], function(){
            var table = layui.table
                ,admin = layui.admin;
            var tableObj = table.render({
                elem: '#test-table-operate'
                ,url: "{{url('admin/api/service_phones')}}"
                ,method:'get'
                ,headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
                ,cols: [[
                    {field:'id',width:70, title: 'ID', sort: true}
                    ,{field:'name', width: 120,title: '名字'}
                    ,{field:'phone', title: '电话'}
                    ,{field:'area_code', title: '区号',align:'center',}
                    ,{field:'disabled_at',width:90, title: '状态', templet:function(row){
                        if(row.disabled_at){
                            return "<span style='color:red;'>已停用</span>";
                        }else{
                            return "<span style='color:green;'>启用中</span>";
                        }
                    }}
                    ,{field:'round',width:90, title: '轮询规则',align:'center'}
                   
                    ,{width:200, align:'center', title: '操作', toolbar: '#test-table-operate-barDemo'}
                ]]
                ,page: true
            });
           
      
            //搜索刷新数据
            var $ = layui.$, active = {
                reload: function(){
                    //执行重载
                    tableObj.reload({
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            search:$('#test-table-demoReload').val(),
                            product_type_id:$('#product_type_id').val(),
                            min:$('#test-laydate-start').val(),
                            max:$('#test-laydate-end').val(),
                                }
                            });
                        }
                    };

                    //监听工具条（操作动作）
                    table.on('tool(test-table-operates)', function (obj) {
                        var data = obj.data;
                        if (obj.event === 'del') {
                            //删除产品
                            var msg = confirm("确定要删除吗？");
                            if (msg) {
                                layer.msg('删除中');
                                $.ajax({
                                    url: "/admin/service_phones/" + data.id,
                                    type: 'post',
                                    data:{
                                        _method:'delete',
                                        _token:"{{ csrf_token() }}"
                                    },
                                    datatype: 'json',
                                    success: function (msg) {
                                        if (msg['err'] == 1) {
                                            layer.msg(msg.str);
                                            //执行重载
                                            tableObj.reload({
                                                page: {
                                                    curr: 1 //重新从第 1 页开始
                                                }
                                                , where: {
                                                    search: $('#test-table-demoReload').val(),
                                                    product_type_id: $('#product_type_id').val(),
                                                    min: $('#test-laydate-start').val(),
                                                    max: $('#test-laydate-end').val(),
                                                }
                                            });
                                        } else if (msg['err'] == 0) {
                                            layer.msg(msg.str);
                                        } else {
                                            layer.msg('删除失败！');
                                        }
                                    }
                                })
                            }
                        } else if (obj.event === 'edit') {
                            //修改产品
                            that.goods_show('修改', '/admin/service_phones/' + data.id + '/edit', 2, 800, 600);
                        } 
                        
                            
                    });

                    //出发搜索事件
                    $('.test-table-reload-btn .layui-btn').on('click', function () {
                        var type = $(this).data('type');
                        active[type] ? active[type].call(this) : '';
                    });

                    //监听排序事件
                    table.on('sort(test-table-operates)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"

                        //尽管我们的 table 自带排序功能，但并没有请求服务端。
                        //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
                        tableObj.reload({
                            initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                            , where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                                field: obj.field //排序字段
                                , order: obj.type //排序方式
                                , search: $('#test-table-demoReload').val() //搜索关键字
                                , product_type_id: $('#product_type_id').val()//产品分类
                                , min: $('#test-laydate-start').val()//开始时间
                                , max: $('#test-laydate-end').val()//结束时间
                            }
                        });
                    });


                    var laydate = layui.laydate;

                    //新增产品
                    $('#addgoods_kind').on('click', function () {
                        that.goods_show('添加', '{{url("admin/service_phones/create")}}',2,800,600);
                    });
                });


                   //产品详情,SKU绑定状态
                function goods_show(title, url, type, w, h) {
                    layer.open({
                        type: type,
                        title: title,
                        area: [w, h],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url
                    });
                }
               
                //跳转到商品列表页
                function goods_info(url, num) {
                    if (num == 0) {
                        layer.msg('该产品无商品绑定');
                    } else {
                        window.location.href = url;
                    }
                }
   
    </script>
@endsection