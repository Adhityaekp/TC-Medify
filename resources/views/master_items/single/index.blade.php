@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-group mb-2">
                    <a href="{{ url('master-items') }}" class="btn btn-secondary">Kembali ke Daftar Item</a>
                </div>
                <div class="card">
                    <div class="card-header">Master Item</div>

                    <div class="card-body">
                        <table>
                            <tr>
                                <th>Nama</th>
                                <td>:</td>
                                <td>{{ $data->nama }}</td>
                            </tr>
                            <tr>
                                <th>Harga Beli</th>
                                <td>:</td>
                                <td>Rp {{ number_format($data->harga_beli, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Laba</th>
                                <td>:</td>
                                <td>{{ $data->laba }}%</td>
                            </tr>
                            <tr>
                                <th>Harga Jual</th>
                                <td>:</td>
                                <td>Rp
                                    {{ number_format($data->harga_beli + ($data->harga_beli * $data->laba) / 100, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>:</td>
                                <td>{{ $data->supplier }}</td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td>:</td>
                                <td>{{ $data->jenis }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>:</td>
                                <td>
                                    @forelse ($data->kategoris as $kt)
                                        <a href="{{ url('kategori-items/view/' . $kt->kode) }}"
                                            class="badge bg-primary text-decoration-none">
                                            {{ $kt->nama }}
                                        </a>
                                    @empty
                                        <span class="text-muted">Belum ada kategori</span>
                                    @endforelse
                                </td>
                            </tr>
                            <tr>
                                <th>Foto</th>
                                <td>:</td>
                                <td>
                                    @if ($data->foto)
                                        <img src="{{ asset('storage/' . $data->foto) }}" width="200"
                                            class="img-thumbnail" alt="{{ $data->nama }}">
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <a class="btn btn-info" href="{{ url('master-items/form/edit') }}/{{ $data->id }}">Edit</a>
                        <a class="btn btn-danger" href="{{ url('master-items/delete') }}/{{ $data->id }}"
                            onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
