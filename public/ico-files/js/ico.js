var clock = $('#countdowner').FlipClock(new Date(ico_start), {
    countdown: true,
    clockFace: 'DailyCounter'
});

$("#redeem_code_form").submit(function(e)
{
    var account_number = $("#account_number");
    var redeem_code_success = $("#redeem_code_success");
    var redeem_code_success_content = $("#redeem_code_success_content");
    var redeem_code_btn = $("#redeem_code_btn");
    var postData = {account_number: account_number.val()};
    var affiliateData;
    var shareThis = $(".sharethis-inline-share-buttons");

    redeem_code_success.hide();
    redeem_code_btn.prop('disabled', true);

    $.ajax(
    {
        url : redeem_code_url,
        type: "POST",
        data : postData,
        success:function(data, textStatus, jqXHR) 
        {
            affiliateData = data.affiliate;
            redeem_code_btn.prop('disabled', false);
            redeem_code_success.show();
            redeem_code_success_content.html(redeemCodeMsg(affiliateData));
            shareThis.attr('data-url', affiliateData.affiliate_url);
            shareThis.attr('data-title', 'Invest in Mimic and get extra MimiCoins :D');
            shareThis.attr('data-image', domain_url+'/img/facebook_share_img.jpg');
            shareThis.attr('data-description', 'This is my affiliate code for Mimic ICO. Go ahead, invest and get some extra MimiCoins :D');
            $(".sharethis-inline-share-buttons .st-btn").css("display", "inline-block");
            account_number.val('');
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            affiliateData = data.affiliate;
            redeem_code_btn.prop('disabled', false);
            redeem_code_success.show();
            redeem_code_success_content.html(redeemCodeMsg(affiliateData));
            account_number.val('');
        }
    });
    e.preventDefault(); //STOP default action

    function redeemCodeMsg(affiliateData)
    {
        return "Code for account <b>"+affiliateData.account_number+"</b> is <b>"+affiliateData.affiliate_code+"</b><br><br>"+
                "Your affiliate url is <b><a href='"+affiliateData.affiliate_url+"'>"+affiliateData.affiliate_url+"</a></b>";
    }
});

$("#newsletter_form").submit(function(e)
{
    var newsletter_email = $("#newsletter_email");
    var newsletter_name = $("#newsletter_name");
    var newsletter_btn = $("#newsletter_btn");
    var newsletter_success = $("#newsletter_success");
    var newsletter_warning = $("#newsletter_warning");
    var postData = {name: newsletter_name.val(), email: newsletter_email.val()};

    newsletter_success.hide();
    newsletter_warning.hide();
    newsletter_btn.prop('disabled', true);

    $.ajax(
    {
        url : newsletter_url,
        type: "POST",
        data : postData,
        success:function(data, textStatus, jqXHR) 
        {
            newsletter_btn.prop('disabled', false);
            newsletter_success.html("Thank you <b>"+data.name+"</b> (<b>"+data.email+"</b>) :)").show();
            newsletter_email.val('');
            newsletter_name.val('');

            setTimeout(function(){ newsletter_success.fadeOut(); }, 3000);
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            newsletter_btn.prop('disabled', false);
            newsletter_warning.html("Email <b>"+newsletter_email.val()+"</b> is already in our database :)").show();
            newsletter_email.val('');
            newsletter_name.val('');

            setTimeout(function(){ newsletter_warning.fadeOut(); }, 3000);
        }
    });
    e.preventDefault(); //STOP default action
});