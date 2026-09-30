@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="form-group mb-2">
                    <a href="{{ url('kategori-items') }}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
                </div>
                <div class="card">
                    <div class="card-header">Kategori Item</div>

                    <div class="card-body">
                        <table>
                            <tr>
                                <th>Kode</th>
                                <td>:</td>
                                <td>{{ $data->kode }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>:</td>
                                <td>{{ $data->nama }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Item</th>
                                <td>:</td>
                                <td>{{ $data->items->count() }}</td>
                            </tr>
                        </table>

                        <h5 class="mt-4">Daftar Item</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Supplier</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->items as $mi)
                                    <tr>
                                        <td>{{ $mi->kode }}</td>
                                        <td>{{ $mi->nama }}</td>
                                        <td>{{ $mi->jenis }}</td>
                                        <td>{{ $mi->supplier }}</td>
                                        <td>
                                            <a href="{{ url('master-items/view/' . $mi->kode) }}"
                                                class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada item di kategori ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <a class="btn btn-info" href="{{ url('kategori-items/form/edit/' . $data->id) }}">Edit</a>
                        <a class="btn btn-success" href="{{ url('kategori-items/pdf/' . $data->kode) }}">Download PDF</a>
                        <a class="btn btn-danger" href="{{ url('kategori-items/delete/' . $data->id) }}"
                            onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
