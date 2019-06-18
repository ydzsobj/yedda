@extends('admin.father.css')
<style>
    .border-color {
        border: 1px solid #333333;
    }
</style>
@section('content')
    <article class="page-container">
        <table class="table table-border table-bordered table-bg">
            <tr class="text-c">
                <th>产品唯一SKU前缀(整体SKU码前四位)</th>
                <th>当前状态</th>
            </tr>

            <tr class="text-c">
                <td rowspan='2'>{{$goods_kind->goods_kind_sku }}</td>
                <td rowspan='2'>@if($goods_kind->goods_kind_sku_status==0) <span
                            color='green'>正常</span> @elseif($goods_kind->goods_kind_sku_status==1) <span
                            style="color:#ccc;">已被释放</span> @else <span style="color:brown;">重用SKU</span> @endif </td>
            </tr>

        </table>
        @if($goods_kind->attrs)
            <form action="{{url('admin/kind/sku_num')}}" method="post" enctype="multipart/form-data"
                  id="form-skuNum-add">
                {{csrf_field()}}

                <table class="table table-border table-bordered table-bg">
                    <tr class="text-c">
                        <th>产品SKU</th>
                        <th>产品SKU属性值</th>
                        <th>库存</th>
                    </tr>

                    @foreach($product_attr as $key=>$value)
                        <input type="hidden" name="product[{{$key}}][kind_sku]"
                               value="{{$value['sku']}}">
                        <input type="hidden" name="product[{{$key}}][goods_kind_sku]"
                               value="{{$goods_kind->goods_kind_sku }}">
                        <input type="hidden" name="product[{{$key}}][sku_x45]"
                               value="{{$value['sku_x45']}}">
                        <input type="hidden" name="product[{{$key}}][sku_x67]"
                               value="{{$value['sku_x67']}}">
                        <input type="hidden" name="product[{{$key}}][sku_x89]"
                               value="{{$value['sku_x89']}}">
                        <input type="hidden" name="product[{{$key}}][goods_kind_id]"
                               value="{{$goods_kind->goods_kind_id }}">
                        <tr class="text-c">
                            <td>{{$value['sku']}}</td>
                            <td>{{$value['val']}}</td>
                            <td><input type="text" value="{{$value['num']}}" name="product[{{$key}}][num]"></td>
                        </tr>
                    @endforeach
                </table>


                <div class="row cl" style="margin-top: 10px;">
                    <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                        <button class="btn btn-primary radius"><i class="Hui-iconfont">&#xe632;</i> 保存并提交</button>
                    </div>
                </div>
            </form>


        @endif
        <br>
        <br>

    </article>
@endsection
@section('js')
    <script type="text/javascript">
        $("#form-skuNum-add").validate({
            rules:{

            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    type: 'post',
                    url: "{{url('admin/kind/sku_num')}}",
                    success: function(data){
                        if(data.err==1){
                            layer.msg('添加成功!',{time:2*1000},function() {
                                //回调
                                index = parent.layer.getFrameIndex(window.name);
                                setTimeout("parent.layer.close(index);",200);

                            });
                        }else{
                            layer.msg(data.msg);
                        }
                    },
                    error: function(XmlHttpRequest, textStatus, errorThrown){
                        layer.msg('error!');
                    }});
                //var index1 = parent.layer.getFrameIndex(window.name);
                //parent.$('.btn-refresh').click();
                /*parent.layer.close(index);*/
            }
        });
    </script>
@endsection
