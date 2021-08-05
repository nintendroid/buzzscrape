/* Initiate a refresh request from the dashboard */
function adminRefreshCache(ajaxurl, bidId) {
    var data = {
        'action': 'bzs_refresh',
        'bid': jQuery('#' + bidId).val()
    };
    
    jQuery('.bsz-admin-container button,input').prop('disabled', true); 

    // Send request
    jQuery.ajax({ 
        type: "POST",
        url: ajaxurl,
        data: data,
        xhrFields: {
            withCredentials: true
        },
        transformRequest: function(obj) {
            var str = [];
            for (var key in obj) {
                str.push(encodeURIComponent(key) + "=" + encodeURIComponent(obj[key]));
            }
            return str.join("&");
        },
        success: function(response) {
            if (response == null || response.success !== true) {
                alert('Failed to retrieve new data. Please check your connection.');
                console.log(response.data);
                return;
            }

            console.log(response.data);
            jQuery('.bsz-admin-container button,input').prop("disabled", false); 
         },
        error: function (xhr) {
            alert(xhr.responseText);
            jQuery('.bsz-admin-container button,input').prop("disabled", false); 
        }
    });
}
