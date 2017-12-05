<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<style type="text/css">
    .green {
        background: #3AD382!important;
    }

    .red {
        background: #F56659!important;
    }
</style> 
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Contribution type</th>
                <th>Forum</th>
                <th>Forum nickname</th>
                <th>Email</th>
                <th>Ethereum address</th>
                <th>Reward</th>
                <th>Previous work</th>
                <th>Approved</th>
                <th>Approve/Dissapprove</th>
                <th>Extra Data</th>
            </tr>
        </thead>
        <tbody>
    @foreach ($contributors as $contributor)
            <tr class="@if($contributor->approved) green @else red @endif">
                <td>{{$contributor->id}}</td>
                <td>{{$contributor->contribution_type}}</td>
                <td>{{$contributor->forum}}</td>
                <td>{{$contributor->forum_nickname}}</td>
                <td>{{$contributor->email}}</td>
                <td>{{$contributor->ethereum_address}}</td>
                <td>{{$contributor->reward}}</td>
                <td>{{$contributor->previous_work}}</td>
                <td>{{$contributor->approved}}</td>
                <td><a href="<?=route('ico-contribute', ['showadmindata' => true, 'id' => $contributor->id, 'approve_dissaprove' => true])?>">Yes / No</a></td>
                <td><form action="<?=route('ico-contribute', ['id' => $contributor->id, 'showadmindata' => true])?>" method="POST">{{ csrf_field() }}<textarea rows="5" cols="50" name="contribution_work">{{$contributor->contribution_work}}</textarea><input type="submit" name="Update"></form></td>
            </tr>
    @endforeach
        </tbody>
    </table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('table').DataTable();
    });
</script>
