@extends('layouts.app')
@section('content')
<h1>{{ ucfirst($resource) }}</h1>
@if(auth()->user()->peran==='penjual')<p><a href="{{ route($resource.'.create') }}">Tambah {{ $resource }}</a></p>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th>@foreach($fields as $field=>$type)<th>{{ ucwords(str_replace('_',' ',$field)) }}</th>@endforeach<th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td>@foreach($fields as $field=>$type)<td>{{ $item->$field }}</td>@endforeach<td>
@if($resource==='penawaran')<small>{{ $item->produk->nama_produk }} — {{ $item->produk->toko->nama_toko }}</small>@endif
<a href="{{ route($resource.'.show',$item->id) }}">Detail</a>
@if(auth()->user()->peran==='penjual') <a href="{{ route($resource.'.edit',$item->id) }}">Edit</a><form class="inline" method="POST" action="{{ route($resource.'.destroy',$item->id) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button>Hapus</button></form>
@elseif($resource==='penawaran')<a href="{{ route('pesanan.create',['penawaran_id'=>$item->id]) }}">Pesan</a>@endif</td></tr>
@empty<tr><td colspan="{{ count($fields)+2 }}">Belum ada data.</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}
@endsection
