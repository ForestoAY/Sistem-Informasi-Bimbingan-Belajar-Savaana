@extends('layouts.backend.app')

@section('title')
    Detail Penerbit
@endsection

@section('content')

<div class="content-wrapper container-xxl p-0">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2>Penerbit : {{$data->name}} </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <section>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable">
                                    <table class="dt-responsive table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>No</th>
                                                <th>No ISBN</th>
                                                <th>No Buku</th>
                                                <th>Nama Buku</th>
                                                <th>Penulis</th>
                                                <th>Tahun Terbit</th>
                                                <th>Kategori</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data->books as $key => $datas)
                                                <tr>
                                                    <td></td>
                                                    <td> {{$key+1}} </td>
                                                    <td> {{$datas->isbn}} </td>
                                                    <td> {{$datas->book_code}} </td>
                                                    <td> {{$datas->name}} </td>
                                                    <td> {{$datas->bookAuthor->author->name}} </td>
                                                    <td> {{$datas->publication_year}} </td>
                                                    <td> {{$datas->category->name}}</td>
                                                    <td>
                                                        <a href="{{route('author.show', $datas->bookAuthor->author->id)}}" class="btn btn-info btn-sm">Detail Penulis</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
