@extends('layouts.app')
@section('content')
    <div class="content-wrapper" style="overflow-y:auto; overflow-x:hidden">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            @foreach (explode('-', $uri) as $info)
                                {{ ucfirst($info) }}
                            @endforeach
                        </h1><br>
                        @if (Session::has('message'))
                            <p class="alert {{ Session::get('alert-class', 'alert-success') }}">
                                {{ Session::get('message') }}</p>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">
                                @foreach (explode('-', $uri) as $info)
                                    {{ ucfirst($info) }}
                                @endforeach
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- /.container-fluid -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                @foreach (explode('-', $uri) as $info)
                                    {{ ucfirst($info) }}
                                @endforeach
                            </h3>

                        </div>
                        <div class="card-body p-0" style="overflow-x:auto;">
                            <table class="table table-striped projects text-center">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">
                                            @sortablelink('game_type', 'Game Type')
                                        </th>
                                        <th style="width: 5%">
                                            @sortablelink('status', 'Status')
                                        </th>
                                        <th style="width: 5%">
                                           Result Color
                                        </th>
                                        <th style="width: 5%">
                                            Total User
                                        </th>
                                        <th style="width: 5%" class="text-center">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($colorData->count() == 0)
                                        <tr>
                                            <td colspan="8" class="text-muted">No record found.</td>
                                        </tr>
                                    @endif
                                    @foreach ($colorData as $data)

                                            <tr>
                                                <td> Color Prediction </td>
                                                <td>
                                                    <!-- @if($data->status == 0)
                                                        Not Started
                                                    @elseif ($data->status == 1)
                                                        Running
                                                    @elseif($data->status == 2)
                                                        End
                                                    @endif -->
                                                    @if($loop->index === 0)
                                                        Running
                                                    @else
                                                        End
                                                    @endif
                                                </td>
                                                <td> {{ !empty($data->result_color) ? $data->result_color : '-' }}</td>
                                                <td>{{ $data->totalColorPredictUser($data->id) }}</td>
                                                <td>
                                                    <a href="{{ url('game-details', $data->id) }}"><button
                                                            class="btn btn-transparent" id="EditUser"><i
                                                                class="fa-solid fa-eye"></i></button>
                                                    </a>
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
@endsection

<!-- Validation -->
@push('child-scripts')
    <script src="{{ asset('js/validate.js') }}"></script>
@endpush
