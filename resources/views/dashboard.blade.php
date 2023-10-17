@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
                    </div>

                </div>
            </div>
            <!-- /.container-fluid -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            {{--  <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{$cowsCount}}</h3>

                                    <p>Pending Calves</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-notifications"></i>
                                </div>
                                <a href="{{ route('pending-calf-list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>  --}}
                        </div>
                        <!-- ./col -->
                        {{--  <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{$cowLotCount}}</h3>

                                    <p>Pending Lots</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-notifications"></i>
                                </div>
                                <a href="{{ route('pending-lot-list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6" hidden>
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>44</h3>

                                    <p>User Registrations</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>  --}}
                        <!-- ./col -->
                    </div>
            </section>
        </section>
    </div>
@endsection
