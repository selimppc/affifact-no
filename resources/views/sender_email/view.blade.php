<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            {{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>--}}
            <h4 class="modal-title text-center">View Of : {{ $data->name }}</h4>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th>Campaign Name</th>
                    <td>{{ isset($data->campaign_id)?$data->relCampaign->name:'' }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ isset($data->name)?$data->name:'' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ isset($data->email)?$data->email:'' }}</td>
                </tr>
                <tr>
                    <th>Smtp Name</th>
                    <td>{{ isset($data->smtp_id)?$data->relSmtp->name:'' }}</td>
                </tr>
                <tr>
                    <th>Imap Name</th>
                    <td>{{ isset($data->imap_id)?$data->relImap->name:'' }}</td>
                </tr>
            </table>
        </div>

        <div class="modal-footer">
            <a href="{{ route('sender-email.index',$data->relCampaign->id) }}" class="btn btn-default" type="button"> Close </a>
        </div>

    </div>
</div>