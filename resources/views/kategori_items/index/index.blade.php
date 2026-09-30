@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Kategori Items</span>
                        <a href="{{ url('kategori-items/form/new') }}" class="btn btn-success btn-sm">Tambah Kategori</a>
                    </div>
                    <div class="card-body">
                        @include('kategori_items.index.filter')
                        <hr>
                        @include('kategori_items.index.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('kategori_items.index.js')
@endsection
