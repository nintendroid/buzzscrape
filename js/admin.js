
function bzsAdminRequest(ajaxurl, data, onSuccess) {
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
            jQuery('.bsz-admin-container button,input').prop("disabled", false); 
            
            if (response == null || response.success !== true) {
                alert('Failed to retrieve new data. Please check your connection.');
                console.log(response.data);
                return;
            }

            if (onSuccess != null) {
                onSuccess();
            }
         },
        error: function (xhr) {
            alert(xhr.responseText);
            jQuery('.bsz-admin-container button,input').prop("disabled", false); 
        }
    });
}


/* Initiate a refresh request from the dashboard */
function adminRefreshCache(ajaxurl, bidId) {
    var data = {
        'action': 'bzs_refresh',
        'bid': jQuery('#' + bidId).val()
    };
    
    return bzsAdminRequest(ajaxurl, data, function () { location.reload(); });
}


/* Enable/disable from the dashboard */
function adminEnableRefresh(ajaxurl, fieldId) {
    var data = {
        'action': 'bzs_autorefresh',
        'autorefresh': jQuery('#' + fieldId).is(':checked') ? 'on' : 'off'
    };
    
    return bzsAdminRequest(ajaxurl, data);
}
