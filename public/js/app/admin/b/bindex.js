!(function($) {
  "use strict";

function getListBots(page) {
//showSpinner('#submit_register', 'Creando tu cuenta')
var alertOptions = {icon: 'error', title:''};
if (!page) {page=1}
  var formData = $('#tokens').serialize();
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&page='+page+'&action=getOwnBotList&controller=admin/b/BController',
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') { 
      listingBots(response.rows)
      if (response.total_pages>1) {
        creaPaginacion(response.total_pages,response.page)
      }

    } 
    else { 
      if (response.message==='noBot') {
        $('#noB').text('No tienes Bots creados.');
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

function listingBots(rows) {
  var rowBody = $('#all_items');

// Vaciar el contenido actual
if (rows.length !== 0) {
  rowBody.empty();
}

$.each(rows, function(index, row) {
var isActive = row.is_active == 1 ? "Activo" : "Inactivo";
var isBlocked = row.is_blocked == 1 ? "Bloqueado" : isActive;

var html = $('<div class="col-12 col-md-3 col-lg-3 col-sm-3"></div>');

// Crear un enlace
var link = $('<a id="b-'+row.bot_id +'" class="" href="' + site_url + 'admin/b/' + row.bot_id + '/"></a>');

// Crear la estructura de la tarjeta
var card = $('<div class="card b-list-card"></div>');
var dot = $(' <div class="action"><i class="gicon-config d-block"> </i></div>');
var cardBody = $('<div class="card-body"></div>');
var divflex =$('<div class="d-flex justify-content-between"></div>')
var heading = $('<h5 class="mt-0 mb-0">' + row.name + '</h5>');
var span = $('<span class="b-status ' + isBlocked + '">'+isBlocked+'</span>');

// Agregar elementos a la tarjeta

divflex.append(heading);
divflex.append(dot);
cardBody.append(divflex);
cardBody.append(span);
card.append(cardBody);
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
    getListBots(numPage)
  } 
});


$(document).on("click", ".next", function(event){
  event.preventDefault();
  var numPage=  parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage+1
  getListBots(numPage)
});

$(document).on("click", ".prev", function(event){
  event.preventDefault();
  var numPage= parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage-1
  getListBots(numPage)
});








// add negocio
$('#submit_add_bot').on('click', function(event) {
    registerBot();
 });

function registerBot(){
  var alertOptions = {icon: 'error', title:''};
  var botName = randomNameGen('Bot') ;
  var formData = $('#tokens').serialize();


  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#submit_add_bot', 'Creando tu Bot')
  formData= formData + '&bot_name='+botName+'&bot_category_val=1';
  formData= formData +'&action=addBot';
  formData= formData +'&controller=admin/b/BController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      showSpinner('#submit_add_bot', 'Añadir Bot', false)
      if(response.status == 'success') {
        var swalOptions = {
          icon: 'success',
          title: '¡Felicidades! Ya se ha creado tu Bot',
          html: '<p>Configuralo para ponerlo online.</p>',
          confirmButtonText:'Configurar Bot',
        };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url + 'admin/b/'+ response.bot_id+'/'
          }  
        }); 
      }
      else { 
          if (response.message==='botExists') {
          swalOptions = {
            icon: 'warning',
            title: '¡Opss!',
            html: '<p>Este alias ya está registrado. Intente con otro</p>',
          };
          swalAlert(swalOptions)
        }
        else if (response.message==='overPlan') {
          swalOptions = {
            icon: 'error',
            title: 'Lo sentimos...',
            html: '<p>Ha superado el límite de Bots gratis. Para registrar otro, por favor, actualice uno de sus bots a un plan superior o compre la capacidad para un Bot con plan superior.</p>',
          };
          swalAlert(swalOptions)
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
          showSpinner('#submit_add_bot', 'Añadir negocio', false)
          alertOptions.title=ajaxError(status, error).title 
          alertToast(alertOptions);
        }
      });
  
}

})(jQuery);


