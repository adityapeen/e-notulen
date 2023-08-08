const sendWA = (prefix = 'admin', csrfToken) => {
    var link = `/${prefix}/wa-blast/send`;
    var msg = $('#message').val();
    var recipients = $('#recipients').val();
    var head = `Sedang mengirim WA Blast`;

    if(msg === ''){
        return Swal.fire({
            title: "Isikan pesan dengan benar",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ya",
            allowOutsideClick: false,
            })
    }

    if(recipients.length === 0){
        return Swal.fire({
            title: "Pastikan memilih penerima dengan benar",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ya",
            allowOutsideClick: false,
            })
    }

    Swal.fire({
      title: "Apakah anda akan mengirimkan pesan ini ?",
      icon: "info",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya",
      cancelButtonText: "Batal",
      allowOutsideClick: false,
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
        title: head,
        });
        var receiver = "";
        var count = 0;
        var interval = 300; // miliseconds
        
        recipients.forEach( (item, index) => {
            setTimeout(function () {
                var data = {
                    message : msg,
                    recipients : item
                };
                $.post({
                    url: link,
                    data: data,
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(response) {
                        count++;
                        receiver += response.results + " <br>";
                        head =
                        count === recipients.length
                            ? "Selesai mengirim WA Blast"
                            : "Sedang mengirim WA Blast";
                        Swal.update({
                        title: head,
                        html: receiver,
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            }, index * interval);
        });       
      }
    });
  };