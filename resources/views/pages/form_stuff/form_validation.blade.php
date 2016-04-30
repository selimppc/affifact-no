@extends('layout.master')
@section('sidebar')
    @parent
    @include('layout.sidebar')
@stop

@section('content')

        <!-- page start-->
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Basic validations
            </header>
            <div class="panel-body">
                <form role="form" class="form-horizontal tasi-form">
                    <div class="form-group has-success">
                        <label class="col-lg-2 control-label">First Name</label>
                        <div class="col-lg-10">
                            <input type="text" placeholder="" id="f-name" class="form-control">
                            <p class="help-block">Successfully done</p>
                        </div>
                    </div>
                    <div class="form-group has-error">
                        <label class="col-lg-2 control-label">Last Name</label>
                        <div class="col-lg-10">
                            <input type="text" placeholder="" id="l-name" class="form-control">
                            <p class="help-block">Aha you gave a wrong info</p>
                        </div>
                    </div>
                    <div class="form-group has-warning">
                        <label class="col-lg-2 control-label">Email</label>
                        <div class="col-lg-10">
                            <input type="email" placeholder="" id="email2" class="form-control">
                            <p class="help-block">Something went wrong</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-danger" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Form validations
            </header>
            <div class="panel-body">
                <div class=" form">
                    <form class="cmxform form-horizontal tasi-form" id="commentForm" method="get" action="">
                        <div class="form-group ">
                            <label for="cname" class="control-label col-lg-2">Name (required)</label>
                            <div class="col-lg-10">
                                <input class=" form-control" id="cname" name="name" minlength="2" type="text" required />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="cemail" class="control-label col-lg-2">E-Mail (required)</label>
                            <div class="col-lg-10">
                                <input class="form-control " id="cemail" type="email" name="email" required />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="curl" class="control-label col-lg-2">URL (optional)</label>
                            <div class="col-lg-10">
                                <input class="form-control " id="curl" type="url" name="url" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="ccomment" class="control-label col-lg-2">Your Comment (required)</label>
                            <div class="col-lg-10">
                                <textarea class="form-control " id="ccomment" name="comment" required></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-danger" type="submit">Save</button>
                                <button class="btn btn-default" type="button">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Advanced Form validations
            </header>
            <div class="panel-body">
                <div class="form">
                    <form class="cmxform form-horizontal tasi-form" id="signupForm" method="get" action="">
                        <div class="form-group ">
                            <label for="firstname" class="control-label col-lg-2">Firstname</label>
                            <div class="col-lg-10">
                                <input class=" form-control" id="firstname" name="firstname" type="text" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="lastname" class="control-label col-lg-2">Lastname</label>
                            <div class="col-lg-10">
                                <input class=" form-control" id="lastname" name="lastname" type="text" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="username" class="control-label col-lg-2">Username</label>
                            <div class="col-lg-10">
                                <input class="form-control " id="username" name="username" type="text" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="password" class="control-label col-lg-2">Password</label>
                            <div class="col-lg-10">
                                <input class="form-control " id="password" name="password" type="password" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="confirm_password" class="control-label col-lg-2">Confirm Password</label>
                            <div class="col-lg-10">
                                <input class="form-control " id="confirm_password" name="confirm_password" type="password" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="email" class="control-label col-lg-2">Email</label>
                            <div class="col-lg-10">
                                <input class="form-control " id="email" name="email" type="email" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="agree" class="control-label col-lg-2 col-sm-3">Agree to Our Policy</label>
                            <div class="col-lg-10 col-sm-9">
                                <input  type="checkbox" style="width: 20px" class="checkbox form-control" id="agree" name="agree" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="newsletter" class="control-label col-lg-2 col-sm-3">Receive the Newsletter</label>
                            <div class="col-lg-10 col-sm-9">
                                <input  type="checkbox" style="width: 20px" class="checkbox form-control" id="newsletter" name="newsletter" />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-danger" type="submit">Save</button>
                                <button class="btn btn-default" type="button">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- page end-->
@stop