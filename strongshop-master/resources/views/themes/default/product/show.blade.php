@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}">
@endpush

@section('content')
@include('layouts.includes.breadcrumb')
<!--主体区域-->
<div class="st-main" id="ST-PRODUCT-SHOW">
    @include('layouts.includes.productShow')
</div>
@endsection
@push('scripts_bottom')
<script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>
<script>
!function () {
    var showToggleProductAjax = '{{config('strongshop.showToggleProductAjax')}}';
    //产品放大镜,移动端产品轮播
    renderProductPictures();
    
    /**
     * 规格提示
     * bootstrap3 - JavaScript 插件 - 工具提示 https://v3.bootcss.com/javascript/#tooltips
     */
    $('[data-toggle="tooltip"]').tooltip();
    
    //当前产品规格
    var specs = @json($price_sepcs);
    console.log('specs', specs);
    //获取当前产品规格值
    var currSpecs = [];
    for (x in specs)
    {
        currSpecs[x] = specs[x].pivot.spec_value;
    }
    currSpecs.sort();
    console.log('currSpecs', currSpecs);
    //spu产品规格
    var spuSpecs = @json($row->spu_specs);
    console.log('spu_specs', spuSpecs);
    //获取spu产品规格值
    var spuSpecsValues=[];
    for(x in spuSpecs){
        spuSpecsValues[x] = spuSpecs[x].spec_values.sort().toString();
    }
    console.log('spu_specs_values', spuSpecsValues);
    //如果当前产品规格大于等于2个 则 标记出全部的无效规格
    if(currSpecs.length >=2){
        markInvalidSpecAll(currSpecs);
    }
    //产品规格点击选择事件
    $(document).on('click', ".st-detail-attr > dl.st-attr > dd", function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).parent().removeClass('active');
            $(this).parent().parent().find("dl>dd").removeClass('st-invalid');
        } else {
            $(this).addClass('active').siblings('dd').removeClass('active');
            $(this).parent().addClass('active');
        }
        //获取已选规格
        var selectedSpecs = [],n=0;
        $(this).parent().parent().children("dl.st-attr").each(function () {
            var childActiveObj = $(this).children('dd.active');
            if(typeof(childActiveObj.attr('data-spec')) != "undefined"){
                selectedSpecs[n] = childActiveObj.attr('data-spec');
                n++;
                $(this).addClass('active');
            }else{
                $(this).removeClass('active');
            }
        });
        console.log('selectedSpecs', selectedSpecs);
        //如果还剩一种规格组未选 则 标记出该规格组无效的规格
        if((currSpecs.length-selectedSpecs.length) === 1){
            var markSpec = $(this).parent().parent().children("dl.st-attr:not(.active)").children('dd');
            markInvalidSpec(markSpec, selectedSpecs);
        }
        //如果规格还未全部选完
        if(selectedSpecs.length !== currSpecs.length){
            console.log('selectedSpecs.length !== currSpecs.length', selectedSpecs.length, currSpecs.length);
            return;
        }
        selectedSpecs.sort();
        console.log('selectedSpecs', selectedSpecs.length, selectedSpecs.toString());
        //如果所选规格和当前规格相同
        if (currSpecs.toString() === selectedSpecs.toString()) {
            console.log('所选规格和当前规格相同', currSpecs.toString(), selectedSpecs.toString());
            return;
        }
        //匹配所选规格
        for (x in spuSpecs)
        {
            if (selectedSpecs.toString() === spuSpecs[x].spec_values.sort().toString()) {
                //如果匹配到则重定向到该产品详情
                var url ='/product?id=' + spuSpecs[x].product_id;
                console.log('匹配到：',url);
                if(!showToggleProductAjax){
                    window.location.href = url;
                    return;
                }
                currSpecs = selectedSpecs;
                url += '&fetchHtml=1';
                layer.load(1);
                $.get(url, function(res){
                    layer.closeAll();
                    $("#ST-PRODUCT-SHOW").html(res.data.content);
                    if(currSpecs.length >=2){
                        markInvalidSpecAll(currSpecs);
                    }
                    renderProductPictures();
                });
                return;
            }
        }
        $(this).removeClass('active').addClass('st-invalid');
    });
    
    /**
     * 标记无效的规格
     * @param {type} obj 需要遍历匹配的规格对象
     * @param selectedSpecs 已选的规格数组
     */
    function markInvalidSpec(obj, selectedSpecs)
    {
        $(obj).each(function (i) {
            var dataSpecVal = $(this).attr('data-spec');
            selectedSpecs.push(dataSpecVal);
            selectedSpecs.sort();
            //匹配无效规格
            if(spuSpecsValues.includes(selectedSpecs.toString())){
                $(this).removeClass('st-invalid');
                console.log('Matching the spec:',dataSpecVal, selectedSpecs, 'valid 有效');
            }else{
                $(this).addClass('st-invalid');
                console.log('Matching the spec:',dataSpecVal, selectedSpecs, 'invalid 无效');
            }
            var indSpec = selectedSpecs.indexOf(dataSpecVal);
            selectedSpecs.splice(indSpec,1);
        });
    }
    /**
     * 标记出全部的无效规格
     */
    function markInvalidSpecAll(currSpecs)
    {
        for(var z=0; z < currSpecs.length; z++){
            var selectedSpecs = JSON.parse(JSON.stringify(currSpecs));
            var zValue = currSpecs[z];
            var zValueInd = selectedSpecs.indexOf(zValue);
            selectedSpecs.splice(zValueInd,1);
            console.log('selectedSpecs', selectedSpecs);
            var markSpec = $(".st-detail-attr>dl.st-attr>dd[data-spec='"+zValue+"']").parent().children('dd');
            markInvalidSpec(markSpec, selectedSpecs);
        }
    }
    function renderProductPictures()
    {
        //产品放大镜
        Util.zoomImage();
        //移动端产品轮播
        if(Util.isIE() === false){
            new Swiper('.swiper-container', {
                pagination: {
                    el: '.swiper-pagination'
                }
            });
        }
    }
}
();
</script>
@endpush