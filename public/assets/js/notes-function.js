const handleDestroy = (id) =>
  Swal.fire({
    title: "Apakah anda yakin menghapus data ini ?",
    // text: "Once deleted, you will not be able to recover this item!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya",
    allowOutsideClick: false,
  }).then((willDelete) => {
    if (willDelete.isConfirmed) {
      var link = "/admin/notes/" + id;
      $("#delete-form").attr("action", link);
      $("#delete-form").submit();
    }
  });

const handleLock = (id) => {
  Swal.fire({
    title: "Apakah anda yakin mengubah status notulensi ini ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya",
  }).then((willLock) => {
    if (willLock.isConfirmed) {
      var link = "/admin/notes/lock/" + id;
      $("#lock-form").attr("action", link);
      $("#lock-form").submit();
    }
  });
};

const handleSend = (id) => {
  Swal.fire({
    title: "Apakah anda akan mengirimkan notulensi ini ?",
    icon: "info",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya",
    cancelButtonText: "Batal",
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      var link = "/admin/notes/attendance/" + id;
      return fetch(link)
        .then((response) => {
          if (!response.ok) {
            throw new Error(response.statusText);
          }
          return response.json();
        })
        .catch((error) => {
          Swal.showValidationMessage(`Request failed: ${error}`);
        })
        .then((result) => {
          if (result.status) {
            var head = `Sedang mengirim notulen`;
            Swal.fire({
              title: head,
            });
            var receiver = "";
            var count = 0;
            result.results.forEach((item) => {
              var url = "/admin/notes/send-mom/" + item.id;
              fetch(url)
                .then((response) => {
                  return response.json();
                })
                .catch((error) => {
                  Swal.showValidationMessage(`Request failed: ${error}`);
                })
                .then((res) => {
                  count++;
                  receiver += res.results + " <br>";
                  head =
                    count === result.results.length
                      ? "Selesai mengirim notulen"
                      : "Sedang mengirim notulen";
                  Swal.update({
                    title: head,
                    html: receiver,
                  });
                });
            });
          }
        });
    }
  });
};

const handleView = (id) => {
  var link = "/admin/notes/view/" + id;
  var url = "/notulensi/";
  $.ajax({
    url: link,
    context: document.body,
  }).done(function (res) {
    if (res.note.file_notulen !== null) {
      url = url + res.note.file_notulen;
      $("#note-file").attr("target", "_blank");
    }
    else {
      url = "#";
      $("#note-file").removeAttr("target");
    }
    var time = res.note.date+' || '+res.note.start_time.substring(0,5)+'-'+res.note.end_time.substring(0,5);
    $("#modal-title").html("<b>"+res.note.name + "</b>");
    $("#note-time").html(time);
    $("#note-issues").html(res.note.issues);
    $("#note-link").attr("href", res.note.link_drive_notulen);
    $("#note-file").attr("href", url);
    $("#note-attendant").empty();
    res.attendants.forEach((item) => {
      $("#note-attendant").append("<li>" + item + "</li>");
    });
    $("#modal-detail").modal("show");
  });
};
