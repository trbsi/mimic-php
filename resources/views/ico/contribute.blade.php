@extends('ico.templates.form.ico-form')

@section('title', 'Invest in Mimic & MimiCoin')

@section('css')
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <style>
        .container {
            margin-top: 6%;
        }

        .terms_conditions {
            padding-left: 20px;
            padding-right: 20px;
            text-align: justify;
        }

        .terms_conditions ul {
            padding-left: 20px;
            padding-right: 20px;
        }

        .swal-wide {
            width: 80%!important;
        }

        .alert {
            font-size: 13px;
        }

        a {
            text-decoration: underline;
        }
        
        caption,th{text-align:left}table{border-collapse:collapse;border-spacing:0;background-color:transparent}caption{padding-top:8px;padding-bottom:8px;color:#777}.table{width:100%;max-width:100%;margin-bottom:20px}.table>tbody>tr>td,.table>tbody>tr>th,.table>tfoot>tr>td,.table>tfoot>tr>th,.table>thead>tr>td,.table>thead>tr>th{padding:8px;line-height:1.42857143;vertical-align:top;border-top:1px solid #ddd}.table>thead>tr>th{vertical-align:bottom;border-bottom:2px solid #ddd}.table>caption+thead>tr:first-child>td,.table>caption+thead>tr:first-child>th,.table>colgroup+thead>tr:first-child>td,.table>colgroup+thead>tr:first-child>th,.table>thead:first-child>tr:first-child>td,.table>thead:first-child>tr:first-child>th{border-top:0}.table>tbody+tbody{border-top:2px solid #ddd}.table .table{background-color:#fff}.table-condensed>tbody>tr>td,.table-condensed>tbody>tr>th,.table-condensed>tfoot>tr>td,.table-condensed>tfoot>tr>th,.table-condensed>thead>tr>td,.table-condensed>thead>tr>th{padding:5px}.table-bordered,.table-bordered>tbody>tr>td,.table-bordered>tbody>tr>th,.table-bordered>tfoot>tr>td,.table-bordered>tfoot>tr>th,.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border:1px solid #ddd}.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}.table-striped>tbody>tr:nth-of-type(odd){background-color:#f9f9f9}.table-hover>tbody>tr:hover,.table>tbody>tr.active>td,.table>tbody>tr.active>th,.table>tbody>tr>td.active,.table>tbody>tr>th.active,.table>tfoot>tr.active>td,.table>tfoot>tr.active>th,.table>tfoot>tr>td.active,.table>tfoot>tr>th.active,.table>thead>tr.active>td,.table>thead>tr.active>th,.table>thead>tr>td.active,.table>thead>tr>th.active{background-color:#f5f5f5}table col[class*=col-]{position:static;float:none;display:table-column}table td[class*=col-],table th[class*=col-]{position:static;float:none;display:table-cell}.table-hover>tbody>tr.active:hover>td,.table-hover>tbody>tr.active:hover>th,.table-hover>tbody>tr:hover>.active,.table-hover>tbody>tr>td.active:hover,.table-hover>tbody>tr>th.active:hover{background-color:#e8e8e8}.table>tbody>tr.success>td,.table>tbody>tr.success>th,.table>tbody>tr>td.success,.table>tbody>tr>th.success,.table>tfoot>tr.success>td,.table>tfoot>tr.success>th,.table>tfoot>tr>td.success,.table>tfoot>tr>th.success,.table>thead>tr.success>td,.table>thead>tr.success>th,.table>thead>tr>td.success,.table>thead>tr>th.success{background-color:#dff0d8}.table-hover>tbody>tr.success:hover>td,.table-hover>tbody>tr.success:hover>th,.table-hover>tbody>tr:hover>.success,.table-hover>tbody>tr>td.success:hover,.table-hover>tbody>tr>th.success:hover{background-color:#d0e9c6}.table>tbody>tr.info>td,.table>tbody>tr.info>th,.table>tbody>tr>td.info,.table>tbody>tr>th.info,.table>tfoot>tr.info>td,.table>tfoot>tr.info>th,.table>tfoot>tr>td.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>thead>tr.info>th,.table>thead>tr>td.info,.table>thead>tr>th.info{background-color:#d9edf7}.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr.info:hover>th,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover{background-color:#c4e3f3}.table>tbody>tr.warning>td,.table>tbody>tr.warning>th,.table>tbody>tr>td.warning,.table>tbody>tr>th.warning,.table>tfoot>tr.warning>td,.table>tfoot>tr.warning>th,.table>tfoot>tr>td.warning,.table>tfoot>tr>th.warning,.table>thead>tr.warning>td,.table>thead>tr.warning>th,.table>thead>tr>td.warning,.table>thead>tr>th.warning{background-color:#fcf8e3}.table-hover>tbody>tr.warning:hover>td,.table-hover>tbody>tr.warning:hover>th,.table-hover>tbody>tr:hover>.warning,.table-hover>tbody>tr>td.warning:hover,.table-hover>tbody>tr>th.warning:hover{background-color:#faf2cc}.table>tbody>tr.danger>td,.table>tbody>tr.danger>th,.table>tbody>tr>td.danger,.table>tbody>tr>th.danger,.table>tfoot>tr.danger>td,.table>tfoot>tr.danger>th,.table>tfoot>tr>td.danger,.table>tfoot>tr>th.danger,.table>thead>tr.danger>td,.table>thead>tr.danger>th,.table>thead>tr>td.danger,.table>thead>tr>th.danger{background-color:#f2dede}.table-hover>tbody>tr.danger:hover>td,.table-hover>tbody>tr.danger:hover>th,.table-hover>tbody>tr:hover>.danger,.table-hover>tbody>tr>td.danger:hover,.table-hover>tbody>tr>th.danger:hover{background-color:#ebcccc}.table-responsive{overflow-x:auto;min-height:.01%}@media screen and (max-width:767px){.table-responsive{width:100%;margin-bottom:15px;overflow-y:hidden;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd}.table-responsive>.table{margin-bottom:0}.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space:nowrap}.table-responsive>.table-bordered{border:0}.table-responsive>.table-bordered>tbody>tr>td:first-child,.table-responsive>.table-bordered>tbody>tr>th:first-child,.table-responsive>.table-bordered>tfoot>tr>td:first-child,.table-responsive>.table-bordered>tfoot>tr>th:first-child,.table-responsive>.table-bordered>thead>tr>td:first-child,.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.table-responsive>.table-bordered>tbody>tr>td:last-child,.table-responsive>.table-bordered>tbody>tr>th:last-child,.table-responsive>.table-bordered>tfoot>tr>td:last-child,.table-responsive>.table-bordered>tfoot>tr>th:last-child,.table-responsive>.table-bordered>thead>tr>td:last-child,.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.table-responsive>.table-bordered>tbody>tr:last-child>td,.table-responsive>.table-bordered>tbody>tr:last-child>th,.table-responsive>.table-bordered>tfoot>tr:last-child>td,.table-responsive>.table-bordered>tfoot>tr:last-child>th{border-bottom:0}}
    </style>
@stop

@section('content')
    <div>
        <h1 class="text-center">Contribute!</h1>
        <h2 class="text-center"><a href="javascript:;" onclick="showRules()">Rules and Terms</a> | <a href="javascript:;" onclick="showHideTable()" id="see_contributors">See contributors</a></h2>
        <br>

        @if($contributors)
        <div id="contributors_table" style="display: none">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Forum nickname
                        </th>
                        <th>Bounty type
                        </th>
                        <th>Prize
                        </th>
                    </tr>
                </thead>
                <tbody>
            @foreach ($contributors as $contributor)
                    <tr>
                        <td>{{$contributor->forum_nickname}}</td>
                        <td>{{$contributor->contribution_type}}</td>
                        <td>{{$contributor->reward}}</td>
                    </tr>
            @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div id="form_div">
            <form id="invest-form" method="POST" action="<?=route('ico-contribute')?>">
                {{ csrf_field() }}
                <label>
                    <span class="label-text">Contribution: </span>
                    <select required name="contribution_type" id="contribution" onchange="showContributionInfo()">
                      <option value="">Select</option>
                      @foreach ($contributionTypes as $key => $value)
                      <option value="{{$key}}">{{$value}}</option>
                      @endforeach
                    </select>
                </label>
                <div class="alert alert-info" id="contribution_info">
                </div>
                <label>
                    <span class="label-text">Forum</span>
                    <select required name="forum">
                      <option value="">Select</option>
                      @foreach ($forums as $key => $value)
                      <option value="{{$key}}">{{$value}}</option>
                      @endforeach
                    </select>
                    <div class="alert alert-info" id="contribution_info">
                    </div>
                </label>
                <label>
                    <span class="label-text">Forum nickname</span>
                    <input type="text" name="forum_nickname" required="">
                </label>
                <label>
                    <span class="label-text">Email</span>
                    <input type="email" name="email" required="">
                </label>
                <label>
                    <span class="label-text">Ethereum address</span>
                    <input type="text" name="ethereum_address" required="">
                </label>
                <label>
                    <span class="label-text">Link's to your blog, website, Medium and/or Steemit profile</span>
                    <textarea required="" name="previous_work" rows=4></textarea>
                </label>

                <div class="alert alert-info" style="display: block">
                    To reserve a language please post your interest with some of your previous translation work.
                    <br>
                    <b>Infographic, white paper and other files will be hosted on our server.</b>
                </div>
                <div class="text-center">
                    <button class="submit" name="submit" id="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('javascript')
     @if($thankYouMsg)
     <script type="text/javascript">
         swal(
          'Good job!',
          "<?=$thankYouMsg?>",
          'success')
     </script>
    @endif

    @if($errorMsg)
    <script type="text/javascript">
         swal(
          'Oops...',
          'All fields are required!',
          'error')
     </script>
    @endif
    <script type="text/javascript">
        var see_contributors = false;
        var container = $(".container");

        function showContributionInfo()
        {
            var contribution_info = $("#contribution_info");
            var text = false;
            switch($("#contribution").val()) {
                case 'white_paper':
                text = "<?=$bounty_info_white_paper?>";
                break;

                case 'topic_manager':
                text = "<?=$bounty_info_infographic?>";
                break;

                case 'article':
                text = "<?=$bounty_info_article?>";
                break;

                default:
                contribution_info.hide();
                break; 
            }

            if(text !== false) {
                contribution_info.html(text).show();
            }
        }

        function showRules()
        {
            swal({
                html: '<div class="terms_conditions"><h1><span>Rules and Terms</span></h1><?=$bounty_rules_conditions_table;?><?=$bounty_rules_conditions?></div>',
                customClass: 'swal-wide',
            })
        }

        function showHideTable()
        {
            see_contributors = !see_contributors;


            if(see_contributors) {
                $("#see_contributors").text('<< Go back');
                container.css({'margin-top': '0', 'max-width': '800px'});
            } else {
                $("#see_contributors").text('See contributors');
                container.css({'margin-top': '6%', 'max-width': '480px'});
            }
            $("#contributors_table").slideToggle();
            $("#form_div").slideToggle();

        }
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#contributors_table table').DataTable();
        });
    </script>
@stop