@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            @foreach($product->skus as $sku)
              <label
              class="btn sku-btn"
              data-price="{{ $sku->price}}"
              data-stock="{{ $sku->stock}}"
              data-toggle="tooltip"
              title="{{ $sku->description }}"
              data-placement="bottom"
              >
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>数量</label><input type="text" class="form-control form-control-sm" value="1"><span>件</span><span class="stock"></span></div>
        <div class="buttons">
          @if($favored)
            <button class="btn btn-success btn-disfavor">取消收藏</button>
          @else
            <button class="btn btn-success btn-favor">❤ 收藏</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" id="product-detail-tab" data-bs-toggle="tab" data-bs-target="#product-detail-tab" type="button" role="tab" aria-controls="product-detail-tab" aria-selected="true">商品详情</button>

        </li>
        <li class="nav-item">
          <button class="nav-link " id="product-reviews-tab" data-bs-toggle="tab" data-bs-target="#product-reviews-tab" type="button" role="tab" aria-controls="product-reviews-tab" aria-selected="true">用户评价</button>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
          <!-- 评论列表开始 -->
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <td>用户</td>
              <td>商品</td>
              <td>评分</td>
              <td>评价</td>
              <td>时间</td>
            </tr>
            </thead>
            <tbody>
              @foreach($reviews as $review)
              <tr>
                <td>{{ $review->order->user->name }}</td>
                <td>{{ $review->productSku->title }}</td>
                <td>{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</td>
                <td>{{ $review->review }}</td>
                <td>{{ $review->reviewed_at->format('Y-m-d H:i') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <!-- 评论列表结束 -->
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  $(document).ready(function () {
    // 点击SKU 边框会被选中
    // 初始化已选中的按钮
    $('.skus .btn-group input[type="radio"]:checked').closest('label.sku-btn').addClass('active');

    // 点击事件处理
    $('.skus .btn-group label.sku-btn').on('click', function() {
        // 移除同组其他按钮的 active 类
        $('.skus .btn-group label.sku-btn').removeClass('active');

        // 添加当前按钮的 active 类
        $(this).addClass('active');

        // 确保对应的 radio 被选中
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // 实时的价格与库存
    $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
    $('.sku-btn').click(function () {
      $('.product-info .price span').text($(this).data('price'));
      $('.product-info .stock').text('库存：' + $(this).data('stock') + '件');
    });

    // 监听收藏按钮的点击事件
    $('.btn-favor').click(function () {
        //发起一个 post ajax 请求，请求url通过后端的route函数生成。
        axios.post("{{ route('products.favor', ['product' => $product->id]) }}")
        .then(function () { // 请求成功后会执行这个回调
          swal('操作成功', '', 'success')
          .then(function() {
            location.reload()
          })
        }, function(error) { // 请求失败会调用这个回调
          // 如果返回码是401 代表没登录
          if (error.response && error.response.status === 401) {
            swal('请先登录！', '', 'error')
          } else if (error.response && (error.response.data.msg || error.response.data.message)) {
            // 其他有msg 或者 message 字段的情况，将msg提示给用户
            swal(error.response.data.msg ? error.response.data.msg : error.response.data.message, '', 'error');
          } else {
            // 其他情况应该是系统出问题了
            swal('系统错误', '', 'error')
          }
        })
    })

    // 监听收藏取消收藏按钮的点击事件
    $('.btn-disfavor').click(function () {
      axios.delete(" {{ route('products.disfavor', ['product' => $product->id]) }} ")
      .then(function () {
        swal('操作成功！', '', 'success')
          .then(function() {
            location.reload()
          })
      })
    })


    // 点击加入购物车按钮点击事件
    $('.btn-add-to-cart').click(function () {

      //请求加入购物车接口
      axios.post(" {{ route('cart.add') }}", {
        sku_id: $("input[name='skus']:checked").val(),
        amount: $(".cart_amount input").val(),
      })
      .then(function () { // 请求成功后执行此回调
        swal('加入购物车成功！', '', 'success')
        .then(function(){
          location.href = '{{ route('cart.index') }}'
        })
      }, function (error) { //请求失败执行此回调
        if (error.response.status === 401) {
          // http 状态码为 401 代表用户未登录
          swal('请先登录！','','error')
        } else if (error.response.status === 422) {
          // http 状态码为422 代表用户输入校验失败
          var html = '<div>'
          _.each(error.response.data.errors, function (errors) {
            _.each(errors, function(error) {
              html += error+'<br>'
            })
          })
          html += '</div>'
          swal({content: $(html)[0], icon: 'error'})
        } else {
          // 其他情况系统出问题了
          swal('系统错误', '', 'error')
        }
      })
    })


  });
  </script>
@endsection
