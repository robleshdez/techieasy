!(function($) {
  "use strict";
 
var iti
var iniPhoneNumber
const input = $("#mobile_number")[0]; // Selecciona el elemento con jQuery y usa [0] para obtener el nodo de HTML
var sseInstance
let sseConnection = null; 
// Asegúrate de que el DOM esté completamente cargado
$(document).ready(function() {
iniPhoneNumber =getFullPhoneNumber()
  //console.log('al iniciar iniPhoneNumber '+iniPhoneNumber)

 iti = window.intlTelInput(input, {
// allowDropdown: false,
// autoPlaceholder: "off",
containerClass: "w-100",
// countryOrder: ["jp", "kr"],
// countrySearch: false,
// customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
//   return "e.g. " + selectedCountryPlaceholder;
// },
// dropdownContainer: document.querySelector('#custom-container'),
// excludeCountries: ["us"],
// fixDropdownWidth: false,
// formatAsYouType: false,
// formatOnDisplay: false,
// geoIpLookup: function(callback) {
//   fetch("https://ipapi.co/json")
//     .then(function(res) { return res.json(); })
//     .then(function(data) { callback(data.country_code); })
//     .catch(function() { callback(); });
// },
// hiddenInput: () => "phone_full",
//i18n: { 'es': 'España' },
initialCountry: "cu",
// nationalMode: false,
// onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
placeholderNumberType: "MOBILE",
// showFlags: false,
separateDialCode: true,
strictMode: true,
//useFullscreenPopup: false,
// validationNumberType: null,
});



  input.addEventListener('input', function () {

//console.log (iti.isValidNumber())
// Validar usando intlTelInput
if (!iti.isValidNumber() && $("#mobile_number").val()!="") {
  input.setCustomValidity('numerito');
}
else{    input.setCustomValidity('');
}


});

 
});


 
function sseConect(sseInstance=false){
if (!sseInstance) {
  if (sseConnection) {
    sseConnection.close();
  }
   return

}
 //instance='5363511379'
      if (typeof (EventSource) !== 'undefined') {
         sseConnection = new EventSource(site_url +'sse.php?sseInstance='+sseInstance);
          

        // const source = new EventSource('http://127.0.0.1:5200/');
        sseConnection.onopen = function (event) {
            console.log('onopen '+ event);
        };
        sseConnection.onerror = function (event) {
            //console.log('onerror '+event);
        };
         
        sseConnection.addEventListener('news', function (event) {

          //console.log(sseConnection);
          console.log('event.data '+event.data);
           if (event.data) {
            var data = JSON.parse(event.data);
            var qrupdated = data.news;
            if (qrupdated!='isNewLogin' && qrupdated!=false && qrupdated!=null && qrupdated!=''&& qrupdated!='noQR') {
                 //$('.qrcontainer h1').text(qrupdated);//para code
                  $('#qrCode').attr('src', qrupdated);//para qr img64
                 /*var options={
                  type: 'image/png',
                  width: 276, // Tamaño del QR
                  height: 276, // Tamaño del QR
                  errorCorrectionLevel: 'M', // Nivel de corrección de errores (L, M, Q, H)
                  margin: 4, // Margen alrededor del QR
                }
                QRCode.toDataURL(qrupdated, options, function (err, url) {
                   
                  //$('#qrCode').attr('src', url);
                });*/
            }
            else if (qrupdated=='isNewLogin' ) {
              Swal.close() 
              sseConnection.close();
            } 
            else if (qrupdated=='noQR' ) {
                //linkUpWhatsApp()
            } 
                
          } 
          else {
            //console.log('no recibo nada')
          }
    
             //source.close(); // disconnect stream
        });


        } 
        else {
            //console.log('Sorry, your browser does not support server-sent events...');
        }
}



$('#show_config').on('click', function() {
// Alternar el texto del botón
if ($(this).text() === "Editar") {
  $(this).text("Configurar");
} else {
  $(this).text("Editar");
}

// Alternar la visibilidad de los elementos
$('#all_flow').toggleClass("d-none");
$('#bot_config').toggleClass("d-none");
$('#add_flow').toggleClass("disabled");
});




function getListFlow(page) {
  var alertOptions = {icon: 'error', title:''};
  if (!page) {page=1}
    var formData = $('#editBot').serialize();
  $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&page='+page+'&action=getBotFLowList&controller=admin/b/BController',
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') { 
        listingFlows(response.rows)
        if (response.total_pages>1) {
          creaPaginacion(response.total_pages,response.page)
        }

      } 
      else { 
        if (response.message==='noFlows') {
          $('#noF').text('No tienes Flows creados');
          $('#all_flow').text('')
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



function listingFlows(rows) {
  var rowBody = $('#all_flow');

// Vaciar el contenido actual
if (rows.length !== 0) {
  rowBody.empty();
}

$.each(rows, function(index, row) {

var html = $('<div class="col-12 col-md-6 col-lg-4 mb-3"></div>');
// Crear un enlace
var link = $('<a id="f_'+row.flow_id +'"   href="' + site_url + 'admin/b/' + row.bot_id + '/f/'+row.flow_id+'"/><i class="gicon-config me-3  d-inline-block"> </i></a>');

var eliminar = $('<a id="df_'+row.flow_id +'" href=""><i class="gicon-trash ms-2 d-inline-block"></i></a></div>')
// Crear la estructura de la tarjeta
var card = $('<div id="flow_'+ row.flow_id + '"class="card b-list-card h-100"></div>');
var divflex = $('<div class="d-flex  justify-content-between "></div>');
var divactions =$('<div class="actions  "></div>')
var cardBody = $('<div class="card-body"></div>');
var heading = $('<h5 class="mt-0 mb-0">' + row.name + '</h5>');
var type = $('<div><span><strong>Tipo:</strong> Flow de '+ row.type +'</span></div>')
let triggerWords = JSON.parse(row.trigger_words);
var words = $('<div><span><strong>Disparadores:</strong> '+ triggerWords[0] +'</span></div>')

// Agregar elementos a la tarjeta
divflex.append(heading);
divflex.append(divactions);
divactions.append(link);
divactions.append(eliminar);
cardBody.append(divflex);
cardBody.append(type);
cardBody.append(words);
card.append(cardBody);

// Agregar el enlace al contenedor principal
html.append(card);
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
    getListFlow(numPage)
  } 
});


$(document).on("click", ".next", function(event){
  event.preventDefault();
  var numPage=  parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage+1
  getListFlow(numPage)
});

$(document).on("click", ".prev", function(event){
  event.preventDefault();
  var numPage= parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage-1
  getListFlow(numPage)
});








//ver cambios en el form del edit para activar guardar

$('#isActive').on('change', function() {//cambio en el estado
  var is_active
  if ($('#isActive').is(':checked')) {
    is_active = 'Activo'
  } else {
    is_active = 'Inactivo'
  } 
  $('.isActive').text(is_active);
  updateBotStatus(is_active)
})


function updateBotStatus(is_active){

  var alertOptions = {icon: 'error', title:''};
  var formData = $('#editBot').serialize();

  /* Mostramos un loader hasta que se procese la solocitud */
  formData= formData +'&action=updateBotStatus';
  formData= formData +'&controller=admin/b/BController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') {
        $('#submit_edit_bot').addClass('disabled')
        alertOptions.title='El Bot está '+is_active 
        alertOptions.icon='success'
        alertToast(alertOptions); 
      }
      else { 

        if (response.message==='notPermission') {toErrorView('Permissions',site_url+'admin/b/')}  
          else if (response.message==='noToken') {toErrorView('403')}
            else if (response.message==1045) {toErrorView('503')}
              else{
                alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
                alertToast(alertOptions);
              }
            }
          },
          error: function(xhr, status, error) { 
            alertOptions.title=ajaxError(status, error).title 
            alertToast(alertOptions);
          }
        });

}


$('#editBot').on('keydown', function(event) {
   if (event.key === 'Enter') {
  event.preventDefault();
  event.stopPropagation();
  }
});


$('#bot_name').on('keyup', function(event) {
 
  if (event.key === 'Enter') {
    var form = $('#editBot');
    if (form[0].checkValidity()) {

      updateBot();
    } else {
// Mostrar mensajes de validación
var datas = {
  "bot_name": {
    "valueMissing": "Por favor, ingresa un nombre para tu Bot.",
    "patternMismatch": "El nombre debe ser mayor de 3 caracteres y menor de 50."
  },
};
validationFeedback(form, datas)
}
form.addClass('was-validated');
}
});

$('#bot_name').on('focusout', function(event) {
  event.preventDefault();
  var form = $('#editBot');
  if (form[0].checkValidity()) {

    updateBot();
  } else {
// Mostrar mensajes de validación
var datas = {
  "bot_name": {
    "valueMissing": "Por favor, ingresa un nombre para tu Bot.",
    "patternMismatch": "El nombre debe ser mayor de 3 caracteres y menor de 50."
  },
};
validationFeedback(form, datas)
}
form.addClass('was-validated');
});


function updateBot(){

  var alertOptions = {icon: 'error', title:''};
  var formData = $('#editBot').serialize();

  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#submit_edit_bot', 'Guardar')
  formData= formData +'&action=updateBot';
  formData= formData +'&controller=admin/b/BController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      showSpinner('#submit_edit_bot', 'Guardar', false)
      if(response.status == 'success') {
        $('#submit_edit_bot').addClass('disabled')

        alertOptions.title='Nombre del Bot actualizado.' 
        alertOptions.icon='success'
        alertToast(alertOptions); 
      }
      else { 
          if (response.message==='notPermission') {toErrorView('Permissions',site_url+'admin/b/')}  
          else if (response.message==='noToken') {toErrorView('403')}
            else if (response.message==1045) {toErrorView('503')}
              else{
                alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
                alertToast(alertOptions);
              }
            }
          },
          error: function(xhr, status, error) { 
            showSpinner('#submit_edit_bot', 'Guardar', false)
            alertOptions.title=ajaxError(status, error).title 
            alertToast(alertOptions);
          }
        });

}



// add flow
$('#add_flow, #sAdd_flow').on('click', function(event) {
  event.preventDefault();
  registerFlow();
});

function registerFlow(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#editBot').serialize();


  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#add_flow', 'Añadir Flujo')
  formData= formData +'&action=addFlow';
  formData= formData +'&controller=admin/f/FController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      showSpinner('#add_flow', 'Añadir Flujo', false)
      if(response.status == 'success') {
        var swalOptions = {
          icon: 'success',
          title: 'Flujo añadido',
          html: '<p>Editalo para crear las respuestas de tu Bot.</p>',
          confirmButtonText:'Editar flujo',
        };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url + 'admin/b/'+ response.bot_id+'/f/'+response.flow_id+'/'
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
            html: '<p>Ha superado el límite de Flujos para este Bot. Para añadir más flujos debe actualizalo a un plan superior.</p>',
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


$(document).on('click', '[id^="df_"]', function(event) {
  event.preventDefault();
  var id = $(this).attr('id').substring(3);
  var name =$(this).parent().parent().find('h5').text();

  var swalOptions = {
    icon: 'warning',
    title: '¡Elimiará el flujo "' + name + '" de forma permanente!',
    confirmButtonText:'Eliminar',
    cancelButtonText:'Cancelar',
    customClass: {
      confirmButton: "btn btn-danger order-2",
      cancelButton: "btn btn-secondary",
    },

    showCancelButton: true,
    focusConfirm: false,
  };
  swalAlert(swalOptions).then((result) => {
    if (result.isConfirmed) {
      delFlow(id)
    }  
  });
})



function delFlow(id){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#editBot').serialize();
  $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&flow_id='+id+'&action=delFlow&controller=admin/f/FController',
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') { 
        getListFlow()
        updateFlowTvsC(response)

        alertOptions.icon = 'success'
        alertOptions.title = 'Flow eliminado.';
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

function updateFlowTvsC(response){
  $('#flowTvsC').text('Flows creados: ' +response.total_flows + ' de ' +response.can_have)
}




$('#del_bot').on('click', function(event) {
  event.preventDefault();
  var name =$('#bot_name').val();

  var swalOptions = {
    icon: 'warning',
    title: '¡Elimiará el Bot "' + name + '" de forma permanente!',
    confirmButtonText:'Eliminar',
    cancelButtonText:'Cancelar',
    customClass: {
      confirmButton: "btn btn-danger order-2",
      cancelButton: "btn btn-secondary",
    },

    showCancelButton: true,
    focusConfirm: false,
  };
  swalAlert(swalOptions).then((result) => {
    if (result.isConfirmed) {
      delBot()
    }  
  });
})



function delBot(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#editBot').serialize();
  var fullPhoneNumber=getFullPhoneNumber()
  unLinkWhatsApp(fullPhoneNumber)
  $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&action=delBot&controller=admin/b/BController',
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') { 
        alertOptions.icon = 'success'
        alertOptions.title = 'Bot eliminado.';
        window.location.href = site_url + 'admin/b/'
        ;
        alertToast(alertOptions);
      } 
      else { 
         if (response.message==='noToken') {toErrorView('403')}
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




$('#wts_cnt').on('click', function(event) {
  var fullPhoneNumber=getFullPhoneNumber()
  //console.log('check ' + iniPhoneNumber)
  event.preventDefault();

  var form = $('#editWts_cnt');

// Validar usando intlTelInput
if (!iti.isValidNumber()&&$("#mobile_number").val()!="") {
  input.setCustomValidity('numerito');
}
else{    input.setCustomValidity('');
}

if (form[0].checkValidity()) {
  
  if (fullPhoneNumber!=iniPhoneNumber && iniPhoneNumber!='') {
    var swalOptions = {
      icon: 'warning',
      title: 'Se eliminará su sesión de WhatsApp',
      html: '<p>No perderá la configuración de su bot, pero se desvinculará de su cuenta de WhatsApp.</p>',
      confirmButtonText:'Aceptar',
      cancelButtonText:'Cancelar',
      customClass: {
        confirmButton: "btn btn-danger order-2",
        cancelButton: "btn btn-secondary",
      },
      showCancelButton: true,
      focusConfirm: false,
    };
    swalAlert(swalOptions).then((result) => {
      if (result.isConfirmed) {
        updateWtsCnt();
      }  
    })
  } else {updateWtsCnt();} 
} else {
// Mostrar mensajes de validación
var datas = {
  "mobile_number": {
    "valueMissing": "Por favor, no olvides el número de móvil.",
    "customError":  "Por favor, ingresa un número de móvil válido.",
  },
};

validationFeedback(form, datas)
}
form.addClass('was-validated');
});


function updateWtsCnt(){

 sseInstance = getFullPhoneNumber()
   sseConect(sseInstance)
 var alertOptions = {icon: 'error', title:''};
  var swalOptions
  var formData = $('#editBot').serialize();
    var fullPhoneNumber =getFullPhoneNumber()
 var countryCode= $('.iti__selected-dial-code').text()
  countryCode=countryCode.slice(1)
 

  /* Mostramos un loader hasta que se procese la solocitud */
  var html=$('#wts_cnt').html()
  showSpinner('#wts_cnt', '')
  formData= formData +'&countryCode='+countryCode+'&fullPhoneNumber='+fullPhoneNumber;
  formData= formData +'&action=updateWtsCnt';
  formData= formData +'&controller=admin/b/BController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      var checked=false
      var disabled='';
      var status='Inactivo';
      if (response.isActive ==1) {
        checked=true;
        status='Activo';
        disabled='';
      } 
      if (response.isBlocked ==1) {
        checked=false;
        status='Bloqueado';
        disabled='disabled bloqued';
      }
      else if(fullPhoneNumber =='') {
        checked=false;
        status='Inactivo';
        disabled='disabled';
      }

      $('.isActive').text(status).removeClass('disabled').addClass(disabled)
      $('#isActive').prop('checked', checked)
      if (checked==true) {
        $('#isActive').removeAttr('disabled')
      }else {$('#isActive').attr('disabled')}

/*if (fullPhoneNumber=="") {
$('#isActive').attr('disabled','')
$('.isActive').addClass('disabled')
}
else {
$('#isActive').removeAttr('disabled','')
$('.isActive').removeClass('disabled')

}*/

showSpinner('#wts_cnt', html, false)
if(response.status == 'success') {
  alertOptions.title='Número de WhatsApp actualizado.' 
  alertOptions.icon='success'
  alertToast(alertOptions);
  
  if (response.sectionWAPI=='eliminate') {
  //console.log(iniPhoneNumber)
  unLinkWhatsApp(iniPhoneNumber)
  } 
  else {
  linkUpWhatsApp()
  }
  iniPhoneNumber=getFullPhoneNumber()
}
else { 
  if (response.message==='numberExists') {
    swalOptions = {
      icon: 'warning',
      title: '¡Opss!',
      html: '<p>Este número está en uso. Intente con otro.</p>',
    };
    swalAlert(swalOptions)
  }
  else if (response.message==='notPermission') {toErrorView('Permissions',site_url+'admin/b/')}  
    else if (response.message==='noToken') {toErrorView('403')}
      else if (response.message==1045) {toErrorView('503')}
        else{
          alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
          alertToast(alertOptions);
        }
      }
    },
    error: function(xhr, status, error) { 
      showSpinner('#wts_cnt', html, false)
      alertOptions.title=ajaxError(status, error).title 
      alertToast(alertOptions);
    }
  });

}

function  linkUpWhatsApp(){
  var alertOptions = {icon: 'error', title:''};
  var swalOptions
  var formData = $('#editBot').serialize();

  var fullPhoneNumber = getFullPhoneNumber()


  /* Mostramos un loader hasta que se procese la solocitud */
  var html=$('#wts_cnt').html()
  showSpinner('#wts_cnt', '')

  formData= formData +'&fullPhoneNumber='+fullPhoneNumber;
  formData= formData +'&action=linkUpWhatsApp';
  formData= formData +'&controller=wapi/WapiController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
       
 
   showSpinner('#wts_cnt', html, false)
   //console.log(response)
if(response.status == 'success') {
 
/**/
/*<h1>${response.qrCode}</h1>*/
    if (response.qrCode) {
        // Mostrar el QR en la alerta si la sesión no está conectada
        swalOptions = {
            icon: 'info',
            title: 'Inicia sesión en WhatsApp',
            html: `
                <p>Utilice el siguiente código para vincular el Bot a tu WhatsApp.</p>
                <div class="qrcontainer">
                <img id="qrCode" src="${response.qrCode}" >
                <img class="logo" src="${site_url}public/img/favicon.png" >
                </div>
                <p>Espera a que se complete la conexión antes de cerrar.</p>
            `,
            showCancelButton: false,
            showCloseButton: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            showConfirmButton: false,  // No mostramos el botón de confirmación, ya que no es necesario
            
        };
    } else {
        swalOptions = {
        icon: 'success',
        title: 'Vinculado con WhatsApp',
        html: '<p>Se ha recuperado exitosamente tu sesión.</p>',
    };
    }
     const $qrCodeElement = $('#qrCode');
    if ($qrCodeElement.length) { //console.log('actualizo qr')
      //$('#qrCode').attr('src', response.qrCode);
    }
    else { //console.log('abro modal')
      swalAlert(swalOptions).then(() => {
      sseConect()
});;}
    
}
else { 
   if (response.message==='uncreated') {
    alertOptions.title = 'Ha ocurrido un error al intentar crear la conexión con WhatsApp.';
          alertToast(alertOptions);
   }  
    else if (response.message==='noToken') {toErrorView('403')}
      else if (response.message==1045) {toErrorView('503')}
        else{
          alertOptions.title = 'Ha ocurrido un error al intentar conectar con la API de WhatsApp.';
          alertToast(alertOptions);
        }
      }
    },
    error: function(xhr, status, error) { 
      showSpinner('#wts_cnt', html, false)
      alertOptions.title=ajaxError(status, error).title 
      alertToast(alertOptions);
    }
  });
}





function  unLinkWhatsApp(fullPhoneNumber){
  sseConect()
  var alertOptions = {icon: 'error', title:''};
  var swalOptions
  var formData = $('#editBot').serialize();
  

  /* Mostramos un loader hasta que se procese la solocitud */
  var html=$('#wts_cnt').html()
  showSpinner('#wts_cnt', '')

  formData= formData +'&fullPhoneNumber='+fullPhoneNumber;
  formData= formData +'&action=unLinkWhatsApp';
  formData= formData +'&controller=wapi/WapiController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      showSpinner('#wts_cnt', html, false)
      if(response.status == 'success') {
        swalOptions = {
        icon: 'success',
        title: 'Sesión eliminada',
        html: '<p>Se ha desvinculado exitosamente de tu WhatsApp.</p>',
    };
    
    swalAlert(swalOptions);
}
else { 
   if (response.message==='undeleted') {
    alertOptions.icon= 'alert';
    alertOptions.title = 'No se ha eliminado la sesión o no existe.';
          alertToast(alertOptions);
   }  
    else if (response.message==='noToken') {toErrorView('403')}
      else if (response.message==1045) {toErrorView('503')}
        else{
          alertOptions.title = 'Ha ocurrido un error al intentar conectar con la API de WhatsApp.';
          alertToast(alertOptions);
        }
      }
    },
    error: function(xhr, status, error) { 
      showSpinner('#wts_cnt', html, false)
      alertOptions.title=ajaxError(status, error).title 
      alertToast(alertOptions);
    }
  });
}


function getFullPhoneNumber() {
  var countryCode= $('.iti__selected-dial-code').text()
  var phoneNumber=$('#mobile_number').val()

  if (phoneNumber.startsWith(countryCode)) {
    phoneNumber = phoneNumber.replace(countryCode, '');
 }
  countryCode=countryCode.slice(1)
  phoneNumber = phoneNumber.replace(/[\s\-()+]/g, '');
   
   
  if ( phoneNumber!='') {
    var fullPhoneNumber = countryCode+phoneNumber
    fullPhoneNumber = fullPhoneNumber.replace("+", "");
  } else  {
    var fullPhoneNumber =''
  }
  //console.log('code: '+countryCode+' fn: '+phoneNumber+' full: '+fullPhoneNumber)
  return fullPhoneNumber
}



})(jQuery);


