jQuery(document).ready(function () {

    jQuery(document).on('submit', '#langswitcher_form', function (e) {

        e.preventDefault();
        if (jQuery("#langswitcher_form input[name=action]").length > 0) {
            jQuery("#langswitcher_form input[name=action]").remove();
            jQuery("#langswitcher_form input[name=security]").remove();
        }

        // We inject some extra fields required for the security
        jQuery(this).append('<input type="hidden" name="action" value="save_langswitcher" />');
        jQuery(this).append('<input type="hidden" name="security" value="' + langswitcer_exchanger._nonce + '" />');

        // We make our call
        jQuery.ajax({
            url: langswitcer_exchanger.ajax_url,
            type: 'post',
            data: jQuery(this).serialize(),
            success: function (response) {
                if (response == "true") {
                    swal({
                        title: "Good job!",
                        icon: "success",
                    });
                } else {
                    swal({
                        title: "Bad Request!",
                        icon: "error",
                    });
                }

                setTimeout(
                    function () {
                        window.location.reload();
                    }, 1500);
            }
        });

    });

});