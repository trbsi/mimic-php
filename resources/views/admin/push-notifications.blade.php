@extends('templates.admin.template')

@section('title')
<title>Send push notification</title>
@endsection

@section('body')
	<div class="container">
	    <div class="row">
	        <div class="col-lg-12">
	            <form id="push-notifications">
	                <div class="form-group">
	                    <label for="title">Title *</label>
	                    <input id="title" class="form-control" type="text" placeholder="Title" maxlength="20" required>
	                </div>
	                <div class="form-group">
	                    <label>Message *</label>
	                    <input id="message" class="form-control" type="text" placeholder="Message" maxlength="100" required>
	                </div>
	                <div class="form-group">
	                    <label>URL to a web page (if any)</label>
	                    <input type="url" class="form-control" type="text" id="url" placeholder="Url" maxlength="100">
	                </div>
	                <div class="form-group">
	                    <label>Password *</label>
	                    <input type="password" class="form-control" id="password" required>
	                </div>
	                <button type="submit" id="submit" class="btn btn-default">Submit</button>
	            </form>
	        </div>
	    </div>
	    <div class="row" style="margin-top: 10px;">
	        <div class="col-lg-12">
	            <div class="alert alert-warning" style="display: none">
	                Sending... Be patient... Stay with me...
	            </div>
	            <div class="alert alert-success" style="display: none">
	                Notifications have been sent!
	            </div>
	            <div class="alert alert-danger" style="display: none">
	                Something is wrong. Did you enter correct password?
	            </div>
	        </div>
	    </div>
	</div>
@endsection

@section('javascript')
	<script>
	    $("#push-notifications").submit(function (e) {
	        e.preventDefault();
	        var formData = {
	            title: $('#title').val(),
	            body: $('#message').val(),
	            url: $('#url').val(),
	            password: $('#password').val()
	        };
	        console.log(formData);
	        $('#submit').attr("disabled", true);
	        $('.alert-danger, .alert-success').hide();
	        $('.alert-warning').show();

	        $.ajax({
	            url: "<?=app('Dingo\Api\Routing\UrlGenerator')->version(strtolower(env('API_VERSION')))->route('push-notifications-token/send-to-everyone');?>",
	            type: "POST",
	            data: formData,
	            success: function (data, textStatus, jqXHR) {
	                //data - response from server
	                $('#submit').attr("disabled", false);
	                $('.alert-success').show();
	                $('.alert-warning').hide();
	                $('#push-notifications').find("input[type=text], input[type=password], input[type=url], textarea").val("");
	            },
	            error: function (jqXHR, textStatus, errorThrown) {
	                $('#submit').attr("disabled", false);
	                $('.alert-danger').show();
	                $('.alert-warning').hide();
	            }
	        });
	    });
	</script>
@endsection
