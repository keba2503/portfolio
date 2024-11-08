require(['jquery', 'loader'], function ($) {

    $(document).ready(function () {
        if ($('body.hdcheckout-index-index').length) {
            $('body').on('click', '.hdcheckout-timeslot-element', function (e) {
                setSelectedTimeslot($(this));
            });
        }
    });

    // Ese evento se escucha en hdcheckout.js para recargar el menú lateral
    function refreshCheckoutSummary() {
        $(document).trigger("refreshCheckoutSummary");
    }

    function setSelectedTimeslot(element) {
        let timeslotId = element.data('timeslot-id');
        let timeslotDate = element.data('date');
        let actionUrl = element.parents('.checkout-horary').data('timeslot-url');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: {timeslot_id: timeslotId, timeslot_date: timeslotDate},
            success: function (response) {
                if (response.success === -1) {
                    window.location.reload();
                } else {
                    refreshCheckoutSummary();
                }
            },
            error: function (event) {
            },
            complete: function (event) {
            }
        });
    }
});
