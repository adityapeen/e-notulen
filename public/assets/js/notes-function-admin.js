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

const handleDeleteChat = (id,type) => {
  Swal.fire({
    title: "Apakah anda yakin menghapus pesan ini ?",
    // text: "Once deleted, you will not be able to recover this item!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya",
    allowOutsideClick: false,
  }).then((willDelete) => {
    if (willDelete.isConfirmed) {
      var link = `/api/delete_message/${id}/${type}`;
      return fetch(link)
        .then((response) => {
          if (!response.ok) {
            throw new Error(response.statusText);
          }
          return response.json();
        })
        .catch((error) => {
          Swal.fire(`Request failed: ${error}`);
        })
        .then((result) => {
          console.log(result)
          if (result.status) {
            var txt = "";
            result.messages.forEach(el => {
                txt += `<li>${el}</li>`
            })
            Swal.fire({
              title: `Hapus Pesan`,
              html: txt,
              allowOutsideClick: false,
            });
          };
        });
    }
  });
}