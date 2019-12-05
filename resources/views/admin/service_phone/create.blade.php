@extends('admin.father.css')
@section('content')
    <article class="page-container">

        {{--新增产品form--}}
        <form class="form form-horizontal" id="form-goodskind-update" enctype="multipart/form-data">
            {{csrf_field()}}
           
          
            <div class="row cl">
                <label for="name" class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>名字：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="" placeholder="" id="name" name="name">
                </div>
            </div>

            <div class="row cl">
                <label for="phone" class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>电话：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="" placeholder="" id="phone" name="phone">
                </div>
            </div>

            <div class="row cl">
                <label for="area_code" class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>区号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="" placeholder="" id="area_code" name="area_code">
                </div>
            </div>

            {{-- <div class="row cl">
                <label for="goods_kind_name" class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>轮询规则</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="" placeholder="" id="name" name="name">
                </div>
            </div> --}}
           
            
            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存并提交</button>
                </div>
            </div>
        </form>
    </article>
@endsection
@section('js')
    <script type="text/javascript">
       

        //表单验证、提交
        $("#form-goodskind-update").validate({
            rules:{
                name:{
                    required:true,
                },
                phone:{
                    required:true,
                    maxlength:20,
                },
                area_code:{
                    required:true,
                    maxlength:5,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    type: 'post',
                    url: "{{url('admin/service_phones')}}",
                    success: function(data){
                        if(data.err==1){
                            layer.msg('添加成功!',{time:2*1000},function() {
                                //回调
                                index = parent.layer.getFrameIndex(window.name);
                                setTimeout("parent.layer.close(index);",2000);
                                window.parent.location.reload();
                            });
                        }else{
                            layer.msg(data.msg);
                        }
                    },
                    error: function(XmlHttpRequest, textStatus, errorThrown){
                        console.log(XmlHttpRequest,textStatus,errorThrown);
                        layer.msg('error!');
                    }});
            }
        });
       
    </script>
@endsection