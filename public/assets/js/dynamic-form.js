function addForm(){
    var html = $(".row-template").html();
    $("#dynamic_form").append(html);
    handlePIC();
    $('.baru-data').last().find('.selection').select2({
        placeholder: 'Pilih PIC',
    });
    $('.baru-data').last().find('input[type="date"]').val(getSeminggu());
    $('.baru-data').last().find('.textform').each(function() {
      ClassicEditor
      .create( this ,{
        height: "300px",
        removePlugins: [ 'Heading' ],
        toolbar: [ 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote' , 'link', 'undo', 'redo']
      })
      .then(editor => {
      editor.model.document.on('change:data', () => {
        this.value = editor.getData();
      });
    })
      .catch( error => {
          console.error( error );
    })
    });

}

function handlePIC(){
    var bykrow = $(".baru-data").length-1;
    var name = 'who['+(bykrow-1)+'][]';
    $('.baru-data').last().find('select').attr('name',name);
}

function sortPIC(){
  var i = -1;
  $('.baru-data').each(function(index){
    var name = 'who['+(i++)+'][]';
    console.log(name);
    $(this).find('select').attr('name',name);  
  });
}

function getSeminggu(){
    var date = new Date();
    date.setDate(date.getDate() + 7);
    var month = (date.getMonth() + 1);               
    var day = date.getDate();
    if (month < 10) 
        month = "0" + month;
    if (day < 10) {
      day = "0" + day;
    }
    var smg = date.getFullYear()+ '-' + month + '-' + day;
    // console.log(smg);
    return smg;
    
}
 
 $("#dynamic_form").on("click", ".btn-tambah", function(){
    addForm()
    $(this).css("display","none")     
    var valtes = $(this).parent().find(".btn-hapus").css("display","");
 })
 
 $("#dynamic_form").on("click", ".btn-hapus", function(){
  swal({
    title: "Apakah anda yakin menghapus data ini ?",
    // text: "Once deleted, you will not be able to recover this item!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then(willDelete => {
      if (willDelete) {
        $(this).parent().parent('.baru-data').remove();
        sortPIC();
        var bykrow = $(".baru-data").length;
        if(bykrow==1){
          $(".btn-hapus").css("display","none")
          $(".btn-tambah").css("display","");
        }else{
          $('.baru-data').last().find('.btn-tambah').css("display","");
        }
      }
  });
 });

async function picAll(event){
  var note_id = $('#note_id').val();
  const result = await $.ajax({
  type: 'GET',
  url: api_all_pic+note_id
  }).then(function (data) {
    var list = JSON.parse(data);
    var idselected = [];
    list.results.forEach(item =>idselected.push(item.id));
    $(event.target.parentNode.querySelector(".selection")).val(idselected).trigger('change');
  });

  return result;
}

      
 
//  $('.btn-simpan').on('click', function () {
//     // $('#dynamic_form').find('input[type="text"], input[type="number"], select, textarea').each(function() {
//     //    if( $(this).val() == "" ) {
//     //       event.preventDefault()
//     //       $(this).css('border-color', 'red');
          
//     //       $(this).on('focus', function() {
//     //          $(this).css('border-color', '#ccc');
//     //       });
//     //    }
//     // })
//     $('#item-list').submit();
//  })