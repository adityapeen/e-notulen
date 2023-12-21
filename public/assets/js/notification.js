
const currentURL = () => {
    var currentURL = window.location;
    // Extract the root URL
    var rootURL = currentURL.protocol + '//' + currentURL.hostname;
    // Extract the port
    var port = currentURL.port;
    var url = `${rootURL}:${port}`;
    return url;
}

const viewNotification = (type, id, notif_id) => {
    url = currentURL();

    if(type == 'evidence'){
        url = `${url}/admin/notes/action/${id}/evidences`
    }
    
    sendMarkRequest(notif_id)
    .then(data => {
        return window.location = url;
      })
      .catch(error => {
        console.error('Error fetching data:', error);
        // Handle errors or inform the user accordingly
      });
}

const markAll = () => {
    let request = sendMarkRequest();
    request.done(() => {
        $('li.notif-item').remove();
        $('#badge-notif').hide()
    })
}

const sendMarkRequest = (id = null) => {
    var _token = $('meta[name="_token"]').attr('content');
    console.log(_token);
    var url = currentURL()+'/mark-as-read';
    return $.ajax(url, {
        method: 'POST',
        data: {
            _token,
            id
        }
    });
}
