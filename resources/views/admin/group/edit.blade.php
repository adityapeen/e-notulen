@extends('admin.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">{{$title}}</h6>
          </div>
        </div>
        <div class="card-body pb-2">
          
          <form action={{ route('admin.groups.update', $group->id)}} method="post">
            {{method_field('put')}}
            @csrf
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama Group Rapat
              </div>
              <div class="col-md-8">
                <input type="text" name="name" id="name" class="form-control border px-1" value="{{ $group->name }}">
              </div>
            </div>
            <div class="mt-3 d-flex" >         
                <button type="submit" class="btn btn-info me-2">Simpan</button>
                <button type="button" onclick="history.back()" class="btn btn-light">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')

@endsection