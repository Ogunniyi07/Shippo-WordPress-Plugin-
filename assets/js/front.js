jQuery(document).ready(function($) {
    $('#tracking-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $results = $('#tracking-results');
        
        $.ajax({
            url: wpShippoTracker.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_shippo_track_shipment',
                nonce: wpShippoTracker.nonce,
                carrier: $form.find('[name="carrier"]').val(),
                tracking_number: $form.find('[name="tracking_number"]').val()
            },
            beforeSend: function() {
                $results.html('<p>Loading tracking information...</p>');
            },
            success: function(response) {
                console.log('API Response:', response);
                if (response.success) {
                    var status = response.data.tracking_status;
                    var html = '<div class="tracking-status">';
                    html += '<h3>Current Status</h3>';
                    html += '<p class="status">' + status.status + '</p>';
                    html += '<p class="details">' + status.status_details + '</p>';
                    if (status.location) {
                        html += '<p class="location">Location: ' + status.location.city + ', ' + status.location.state + '</p>';
                    }
                    html += '<p class="date">Updated: ' + new Date(status.status_date).toLocaleString() + '</p>';
                    html += '</div>';
                    
                    $results.html(html);
                } else {
                    console.log('Error Response:', response.data);
                    $results.html('<p class="error">' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                $results.html('<p class="error">Failed to retrieve tracking information. Please try again. Error: ' + error + '</p>');
            }
        });
    });
});