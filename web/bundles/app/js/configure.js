$(function () {

    $('#email-config').find('.edit-email').click(function () {
        var email = $(this).data('email');
        var id = $(this).data('id');
        var $modal = $('#email-modal');
        $modal.modal({
            show: true
        });
        $modal.find('.email-input').val(email);
        $modal.find('.save-btn').click(function () {
            $modal.find('.save-btn').attr("disabled", true);
            var newEmail = $modal.find('.email-input').val();
            if (email == newEmail) {
                console.log("same");
            }
            else {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "updateemail",
                    data: {id: id, email: newEmail},
                    success: function (response) {
                        if (response.success) {
                            $modal.modal('hide');
                            location.reload();
                        }
                    }
                });
            }
            $modal.find('.save-btn').removeAttr("disabled");
        });
    });

    $('#create-email').click(function () {
        var $modal = $('#email-modal');
        $modal.modal({
            show: true
        });
        $modal.find('.save-btn').click(function () {
            $modal.find('.save-btn').attr("disabled", true);
            var newEmail = $modal.find('.email-input').val();
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "createemail",
                data: {email: newEmail},
                success: function (response) {
                    if (response.success) {
                        $modal.modal('hide');
                        location.reload();
                    }
                }
            });
            $modal.find('.save-btn').removeAttr("disabled");
        });
    });

    $('#save-options').click(function () {
        var tempLimit = $('#temp-limit').val();
        var lightLimit = $('#light-limit').val();
        if($('#notify-email').val() == "Sí"){
            var notifyEmail = 1;
        } else {
            var notifyEmail = 0;
        }
        if($('#notify-twitter').val() == "Sí"){
            var notifyTwitter = 1;
        } else {
            var notifyTwitter = 0;
        }
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "updateconfig",
            data: {tempLimit: tempLimit, lightLimit: lightLimit, notifyEmail: notifyEmail, notifyTwitter: notifyTwitter},
            success: function (response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });

});