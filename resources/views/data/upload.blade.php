@extends('layouts.app') @section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">	
				<div class="card">
					<div class="card-header">File List</div>
					<div class="card-body">
						<form method="POST" action="{{ url('data/down') }}">
						@csrf
						@foreach ($datas as $data)
							<div class="form-group row">
								<label for="name" class="col-md-3 col-form-label text-md-right">FileName: </label> 
								<label for="name" class="col-md-7 col-form-label">{{ $data['filename'] }}</label>
								@if ($data['status'] == false)
								 	<label for="name" class="col-form-label">Calculating</label>								 	
								 @else 
								 	<button type="submit" class="col-form-label btn btn-primary" name="submit" value="{{ $data['id']}}">下载</button>
								 	<label for="name" class="col-md-8 col-form-label text-md-right">{{$data['report']}}</label>
								 @endif
							</div>
						@endforeach
						</form>
						<br/><br/><br/>
						<form method="POST" action="{{ url('data/upload') }}" enctype="multipart/form-data">
						@csrf
						<div class="form-group row">
							<label for="name" class="col-md-3 col-form-label text-md-right">Data File: </label>
							<input type="file" class="col-md-7 col-form-label text-md-left" name="datafile" required>
						</div>
						<div class="form-group row">
							<label for="name" class="col-md-3 col-form-label text-md-right">Reference File: </label>
							<input type="file" class="col-md-7 col-form-label text-md-left" name="referencefile" required>
						</div>
						<div class="form-group row">
							<label for="name" class="col-md-3 col-form-label text-md-right">CFDs:</label>
							<input type="radio" class="col-form-label"  name="rule" value="CFDs" checked >
						</div>
						<div class="form-group row">
							<label for="name" class="col-md-3 col-form-label text-md-right"></label>
							<button type="submit" class="col-md-5 col-form-label btn btn-primary">Upload</button>
						</div>
						</form>
					</div>
				</div>
			
		</div>

	</div>
</div>
@endsection
