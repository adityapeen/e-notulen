const prepareDropdown = () => {
  var currentURL = window.location.href;
  var pattern = /notes\/satker\/([^/?]+)/i;
  // Check if the URL matches the pattern
  var match = currentURL.match(pattern);
  
  if (match) {
      // Extract the IDs
      var satker_id = match[1];
      $('#satker_code').val(satker_id);
  }
  else {
    var pattern = /action-items\/([^/?]+)/i;
    // Check if the URL matches the pattern
    var match = currentURL.match(pattern);
    if (match) {
        // Extract the IDs
        var satker_id = match[1];
        $('#satker_code').val(satker_id);
    }
  }
}

const filterNote = () => {
  var id = $('#satker_code').val();
  var segments = $(location).attr('href').split('/');
  var role = segments[3];
  var link = `/${role}/notes/satker/${id}`;
  window.location.href = link;  
};

const filterAction = () => {
  var id = $('#satker_code').val();
  var segments = $(location).attr('href').split('/');
  var role = segments[3];
  var link = `/${role}/action-items/${id}`;
  window.location.href = link;  
};