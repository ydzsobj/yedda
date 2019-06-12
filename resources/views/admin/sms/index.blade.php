@extends('admin.father.css')
@section('content')
    <div class="page-container">
        <table id="table" class="table table-border table-bordered table-bg">
            <thead>
            <tr>
                <th scope="col" colspan="9">短信列表</th>
            </tr>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="" value=""></th>
                <th width="40">ID</th>
                <th width="90">手机</th>
                <th>内容</th>
                <th width="130">加入时间</th>
                <th width="100">返回值</th>
                <th width="100">返回信息</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $val)
            <tr class="text-c">
                <td><input type="checkbox" value="{{$val->id}}" name=""></td>
                <td>{{$val->id}}</td>
                <td>{{$val->mobile}}</td>
                <td>{{$val->sms_text}}</td>
                <td>{{$val->send_time}}</td>
                <td class="td-status">{{$val->code}}</td>
                <td class="td-manage">{{$val->code_msg}}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
<script type="text/javascript" src="/admin/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/admin/datatables/1.10.0/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $('table').dataTable({
            "aaSorting":[[1,"desc"]]
        });
    </script>
@endsection
