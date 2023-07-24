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
}

const filterNote = () => {
  var id = $('#satker_code').val();
  var link = `/admin/notes/satker/${id}`;
  window.location.href = link;  
};