!(function($) {
  "use strict";
 
const editMediaModal = new bootstrap.Modal('#editMediaModal')

  function getListMedias(page=1) {
//showSpinner('#submit_register', 'Creando tu cuenta')
var alertOptions = {icon: 'error', title:''};
  var formData = $('#tokens').serialize();
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&page='+page+'&action=getMediasList&controller=admin/m/MController',
  
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') { 
      listingMedias(response.rows)
      if (response.total_pages>1) {
        creaPaginacion(response.total_pages,response.page)
      }
      else {
        $('#all_items_pagination').empty()
      }

    } 
    else { 
      if (response.message==='noMedias') {
         $('#all_items').empty()
         var rowBody = $('#all_items');
         var span='<span id="noM">No tienes imágenes agregadas a este negocio.</span>'
        rowBody.append(span)
      }
      else if (response.message==='noToken') {toErrorView('403')}
        else if (response.message==1045) {toErrorView('503')}
          else{
            alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
            alertToast(alertOptions);
          }
        }
      },
      error: function(xhr, status, error) { 
        //showSpinner('#submit_register', 'Crear cuenta', false)
        alertOptions.title=ajaxError(status, error).title 
        alertToast(alertOptions);
      }
    });
}

function listingMedias(rows) {
  var rowBody = $('#all_items');

// Vaciar el contenido actual
if (rows.length !== 0) {
  rowBody.empty();
}

$.each(rows, function(index, row) {

var mediaURL=getThumbnailUrl(row.media_url, 'small')
var html = $('<div class="col-4 col-md-2 col-lg-2 col-sm-2"></div>');

// Crear un enlace
var link = $('<a id="mID_'+row.media_id+'" class="m-list-card" href="#"></a>');

// Crear la estructura de la tarjeta
var card = $('<div class="card mb-3"></div>');
var image = $('<img class="card-img-top" src="' + site_url + mediaURL+'">');

// Agregar elementos a la tarjeta
card.append(image);
link.append(card);

// Agregar el enlace al contenedor principal
html.append(link);
// Agregar el nuevo elemento al contenedor del cuerpo de la fila
rowBody.append(html);
});
}

function creaPaginacion(total_pages,page){
// Obtener el elemento tbody de la tabla
var upbody = $('#all_items_pagination');
upbody.empty()
var li=''
var item=''
//primer item
if (page==1) {
  li = $('<li class="page-item disabled">')
  item='<span class="page-link">Anterior</span>'
}
else{
  li = $('<li class="page-item ">') 
  item='<a class="page-link prev" href="#">Anterior</a>'
}
li.append(item)
upbody.append(li)

for (var i = 1; i <= total_pages; i++) {

  var li = $('<li>');
//estoy en la misma pagina q el elemento q imprimo
if (page==i) {
  li = $('<li class="page-item active" aria-current="page">')
  item='<span class="page-link">'+i+'</span>'
  li.append(item)
  upbody.append(li)
}
else{
  li = $('<li class="page-item">')
  item='<a class="page-link linkeable" href="#">'+i+'</a>'
  li.append(item)
  upbody.append(li)
}
}

//ultimo item
if (page==total_pages) {
  li = $('<li class="page-item disabled">')
  item='<span class="page-link">Siguiente</span>'
}
else{
  li = $('<li class="page-item ">') 
  item='<a class="page-link next" href="#">Siguiente</a>'
}
li.append(item)
upbody.append(li)
}

//Add short link
$(document).on("click", ".linkeable", function(event){
  event.preventDefault();
  var numPage=$(this).text();
  if (!isNaN(numPage)) {
    getListMedias(numPage)
  } 
});


$(document).on("click", ".next", function(event){
  event.preventDefault();
  var numPage=  parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage+1
  getListMedias(numPage)
});

$(document).on("click", ".prev", function(event){
  event.preventDefault();
  var numPage= parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage-1
  getListMedias(numPage)
});



function getThumbnailUrl(mediaUrl, size) {
    var extension = mediaUrl.split('.').pop();
    var fileNameWithoutExtension = mediaUrl.replace(/\.[^.]*$/, '');
    var thumbnailName = fileNameWithoutExtension + '-' + size + '.' + extension;
    return thumbnailName;
  }

$(document).on('click', '[id^="mID_"]', function() {
   event.preventDefault();
  var id = $(this).attr('id').substring(4);
  $("#media_id").val(id);
    getMediaDetails()
   
})


function getMediaDetails(){
  //showSpinner('#submit_register', 'Creando tu cuenta')
var alertOptions = {icon: 'error', title:''};
var formData = $('#editMedia').serialize();
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&action=getMediaDetails&controller=admin/m/MController',
  
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') { 
      $('#upload_date').text(response.row.created_at)
      $('#file_weight').text(response.row.mediaInfo.file_weight)
      $('#file_type').text(response.row.mediaInfo.file_info.mime)
      var ancho = response.row.mediaInfo.file_info['0']
      var alto = response.row.mediaInfo.file_info['1']
      $('#file_size').text(ancho+' por '+ alto+' píxeles')
      $('#img_name').val(response.row.name)
      //$('#alt_text').val(response.row.alt_text)
      $('#img_url').text(site_url+ response.row.media_url)
      $('#img_url').attr('href', site_url+ response.row.media_url)
      $('#img_details').attr('src', site_url+ response.row.media_url)
      $('#img_details').prop('alt', response.row.alt_text)
       editMediaModal.show() 
    } 
    else { 
      if (response.message==='noMedia') {toErrorView('404')}
      else if (response.message==='noToken') {toErrorView('403')}
        else if (response.message==1045) {toErrorView('503')}
          else{
            alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
            alertToast(alertOptions);
          }
        }
      },
      error: function(xhr, status, error) { 
        //showSpinner('#submit_register', 'Crear cuenta', false)
        alertOptions.title=ajaxError(status, error).title 
        alertToast(alertOptions);
      }
    });
}

$(document).on('click', '#delmedia', function(e) {
   e.preventDefault();
var swalOptions = {
      icon: 'warning',
      title: '¡Elimiará la imagen de forma permanente!',
      confirmButtonText:'Eliminar',
      cancelButtonText:'Cancelar',
      customClass: {
      confirmButton: "btn btn-tercero order-2",
      cancelButton: "btn btn-primary",
    },
    
      showCancelButton: true,
      focusConfirm: false,
    };
    swalAlert(swalOptions).then((result) => {
      if (result.isConfirmed) {
         delMedia()
      }  
    });
   
   
})
 
function delMedia(){
  //showSpinner('#submit_register', 'Creando tu cuenta')
var alertOptions = {icon: 'error', title:''};
var formData = $('#editMedia').serialize();
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&action=delMedia&controller=admin/m/MController',
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') { 
      getListMedias()
      updateSpaceUsed(response)
      editMediaModal.hide() 

      alertOptions.icon = 'success'
      alertOptions.title = 'El archivo <strong>'+$('#img_name').val()+'</strong> se ha eliminado.';
              ;
      alertToast(alertOptions);
    } 
    else { 
      if (response.message==='noMedia') {
        toErrorView('404')
      }
      else if (response.message==='noToken') {toErrorView('403')}
        else if (response.message==1045) {toErrorView('503')}
          else{
            alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
            alertToast(alertOptions);
          }
        }
      },
      error: function(xhr, status, error) { 
        //showSpinner('#submit_register', 'Crear cuenta', false)
        alertOptions.title=ajaxError(status, error).title 
        alertToast(alertOptions);
      }
    });
}

function updateSpaceUsed(response){
  var totalSpace = response.total_space;
  var spacePlan=response.disk_capacity;
$('#spaceUsed').text(totalSpace + 'MB usados de ' + spacePlan + 'MB');
  }
 

$('#img_name').on('input', function () {
    $('#editMedia_guardar').removeClass('disabled');
});


$('#editMedia_guardar').on('click', function (e) {
  e.preventDefault();
  editMedia()
});

function editMedia() {
    //showSpinner('#submit_register', 'Creando tu cuenta')
var alertOptions = {icon: 'error', title:''};
var formData = $('#editMedia').serialize();
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&action=editMedia&controller=admin/m/MController',
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') { 
      editMediaModal.hide() 
      alertOptions.icon = 'success'
      alertOptions.title = 'Los cambios en <strong>'+$('#img_name').val()+'</strong> se guardaron correctamente.';
      alertToast(alertOptions);
    } 
    else { 
      if (response.message==='noMedia') {
        toErrorView('404')
      }
      else if (response.message==='noToken') {toErrorView('403')}
        else if (response.message==1045) {toErrorView('503')}
          else{
            alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
            alertToast(alertOptions);
          }
        }
      },
      error: function(xhr, status, error) { 
        //showSpinner('#submit_register', 'Crear cuenta', false)
        alertOptions.title=ajaxError(status, error).title 
        alertToast(alertOptions);
      }
    });

}


const $editMediaModal= $('#editMediaModal')
$editMediaModal.on('hidden.bs.modal', event => {
$('#editMedia_guardar').addClass('disabled');
$('#img_details').attr('src', '')
$('#img_details').prop('alt', '')
 })

})(jQuery);


