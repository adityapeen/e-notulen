const refreshComments = () => {
    var currentURL = window.location.href;
    var pattern = /notes\/action\/([^/?]+)\/evidences/i;
    var match = currentURL.match(pattern);
    var id;
  
    if (match) {
        id = match[1]; // Extract the IDs
    }
    $('#comments-card').empty();
    var url = `/api/comments/${id}`;
    fetch(url)
      .then(response => response.json()) // Parse the response as JSON
    .then(data => {
      data.forEach(item => {
        $('#comments-card').append(item)
      });
       container.scrollTop = container.scrollHeight;
    })
    .catch(error => {
      console.error('Error fetching data:', error);
    });
  }
  
  const sendComment = (action_id) => {
    var message = $('#message').val();
    var _token = $('meta[name="_token"]').attr('content');
    var url = `/api/comments`;
    var data = {
      _token, action_id, message
    }
    $.ajax({
      type: "POST",
      url: url,
      data: data,
    }).done(function() {
      $('#message').val("");
      refreshComments();
    })
    .fail(function() {
      alert( "error" );
    });
  }