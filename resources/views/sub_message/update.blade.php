@extends('layout.master')
@section('sidebar')
@parent
@include('layout.sidebar')
@stop

@section('content')
        <!-- page start-->
<div class="row">
    <div class="col-lg-8">
        <section class="panel">

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(Session::has('flash_message'))
                <div class="alert alert-success">
                    <p>{{ Session::get('flash_message') }}</p>
                </div>
            @endif
            @if(Session::has('flash_message_error'))
                <div class="alert alert-danger">
                    <p>{{ Session::get('flash_message_error') }}</p>
                </div>
            @endif



            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{$pageTitle}}
                    </header>
                    <div class="panel-body">
                        {!! Form::model($data, ['files'=>true,'method' => 'PATCH', 'route'=> ['sub-message.update', $data->id]]) !!}

                        {!! Form::hidden('title','RE:{subject}' ) !!}
                        <div class="form-group">
                            {!! Form::label('description', 'Description:', ['class' => 'control-label']) !!}
                            {!! Form::textarea('description', Input::old('description'), ['class' => 'wysihtml5 form-control form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('start_time', 'Start Time:', ['class' => 'control-label']) !!}
                            {!! Form::text('start_time', Input::old('start_time'), ['class' => 'form_datetime form-control','required'=>'true']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('end_time', 'End Time:', ['class' => 'control-label']) !!}
                            {!! Form::text('end_time', Input::old('end_time'), ['class' => 'form_datetime form-control','required'=>'true']) !!}
                        </div>



                        <div class="fileupload fileupload-new btn btn-white btn-file fileupload-new fileupload-exists" data-provides="fileupload">
                            <i class="icon-paper-clip"></i> Select file</span>
                            {!! Form::file('attchment[]', array('multiple'=>true,'class'=>'default')) !!}
                            <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none; margin-left:5px;"></a>
                        </div>
                        <div>
                            {!! Form::hidden('message_id',$message_id ) !!}
                            {!! Form::hidden('campaign_id',$campaign_id ) !!}
                        </div>

                        <div class="form-group">
                            @if(isset($message_data))
                                @foreach($message_data as $attachments)
                                    @if($attachments->file_type == 'image')
                                        <br>
                                        <div class="row">
                                            <div id="{{ $attachments->id  }}">
                                                <a href="{{ route('sub-message.image.show', $attachments->id) }}"  data-toggle="modal" data-target="#imageView"><img src="{{ URL::to($attachments->file_name) }}" height="60px" width="60px" alt="{{$attachments->file_name}}" />
                                                </a>
                                                <span style="cursor:pointer" class="btn-danger" onclick="deleteFile(this.id)" id="{{ $attachments->id }}" ><i class="icon-trash"></i>Delete </span>
                                            </div>
                                        </div>

                                    @else
                                        <br>
                                        <div class="row">
                                            <div id="{{ $attachments->id  }}">
                                                <a href="{{ URL::to($attachments->file_name) }}" onclick="return confirm('Are you sure to Download?')" download>
                                                    <img src="{{ URL::to('default-images/file.png') }}" height="60px" width="60px" alt="{{$attachments->file_name}}" />
                                                    {{$attachments->file_name}}
                                                </a>
                                                <span style="cursor:pointer" class="btn-danger" onclick="deleteFile(this.id)" id="{{  $attachments->id }}" ><i class="icon-trash"></i>Delete </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group">
                            <a href="{{ route('sub-message.index',$message_id.'/'.$campaign_id) }}" class="btn btn-default" type="button"> Close </a>
                            {!! Form::submit('Submit', ['class' => 'btn btn-success']) !!}
                        </div>

                        <p> &nbsp; </p>


                    </div>

                </section>
            </div>



        </section>
    </div>
</div>
<!-- page end-->

<!-- Modal  -->
<div class="modal fade" id="etsbModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<!-- modal -->

<!--script for this page only-->
@if($errors->any())
    <script type="text/javascript">
        $(function(){
            $("#addData").modal('show');
        });
    </script>
@endif
<script type="text/javascript">
    $('#imageView').click(function(e) {
        e.preventDefault();
        $('#imageView')
                .removeData()
    });

    function deleteFile(id){
        var confirm_message = confirm('Are you sure');
        if(confirm_message) {
            var msg_id = id;
            $.ajax({
                url: '{{URL::to("attachment/destroy-file")}}/'+msg_id,
                type: 'GET',
                //data: { id: message_id },
                success: function(response)
                {
                    $( "#"+msg_id+"" ).remove();
                    alert("successfully deleted");

                }
            });
        }

    }
</script>
@stop