<div class="product_image">
	        <img src="{{\App\img::where('img_goods_id',$goods->goods_id)->first()->img_url}}">
	    </div>
	    <div class="check_show">
	        <h2><b>nama Produk：</b>{{$goods->goods_name}}</h2>
	        <h2><b>Nomor pesanan Anda: </b>{{$order->order_single_id}}</h2>
	        <h2><b>Status pesanan Anda: </b>@if($order->order_type==0)Belum terverifikasi @elseif($order->order_type==1)Sudah terverifikasi @elseif($order->order_type==2)<span style="color: red;">Verifikasi gagal</span>@elseif($order->order_type==3)Sudah kirim ke jasa pengiriman elseif($order->order_type==4)Sudah terima barang elseif($order->order_type==5||$order->order_type==6||$order->order_type==7)<span style="color: red;">Pengembalian sedang diproses</span>@elseif($order->order_type==8)<span style="color: red;">Menolak terima barang</span>@else &nbsp; @endif</h2>
	        <h2><b>Perusahaan logistik：</b>@if($order->order_send_type==null)Belum ada informasi jasa pengiriman @else{{$order->order_send_type}}@endif</h2>
	        <h2><b>Nomor pesanan / nomor logistik (silahkan isi salah satu ):</b>@if($order->order_send==null)Belum ada informasi logistik @else{{$order->order_send}}@endif</h2>
</div>