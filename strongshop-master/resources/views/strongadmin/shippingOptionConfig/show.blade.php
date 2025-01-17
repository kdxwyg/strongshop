@extends('strongadmin::layouts.app')

@push('styles')
<style></style>
@endpush

@push('scripts')
<script></script>
@endpush

@section('content')
<div class="st-h15"></div>
<table class="layui-table" lay-size="sm">
    <colgroup>
        <col width="150"><col>
        <col><col>
        <col width="150"><col>
        <col><col>
    </colgroup>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center;background: #eee;">基本信息</th>
        </tr> 
    </thead>
    <tbody>
        <tr>
            <td><strong>{{$model->getAttributeLabel('title')}}</strong></td>
            <td>{{$model->title}}</td>
            <td><strong>{{$model->getAttributeLabel('shipping_option_id')}}</strong></td>
            <td>{{$model->shipping_option_id}}</td>
        </tr>
        <tr>
            <td><strong>{{$model->getAttributeLabel('countries')}}</strong></td>
            <td>{{$model->countries}}</td>
            <td><strong>{{$model->getAttributeLabel('states')}}</strong></td>
            <td>{{$model->states}}</td>
        </tr>
        <tr>
            <td><strong>{{$model->getAttributeLabel('more')}}</strong></td>
            <td>{{$model->more}}</td>
            <td><strong>{{$model->getAttributeLabel('status')}}</strong></td>
            <td>{{$model->status}}</td>
        </tr>
        <tr>
            <td><strong>{{$model->getAttributeLabel('created_at')}}</strong></td>
            <td>{{$model->created_at}}</td>
            <td><strong>{{$model->getAttributeLabel('updated_at')}}</strong></td>
            <td>{{$model->updated_at}}</td>
        </tr>
    </tbody>
</table>
@endsection

@push('scripts_bottom')
<script>
    !function () {
        //...
    }();
</script>
@endpush
