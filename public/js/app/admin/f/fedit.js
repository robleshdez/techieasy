!(function($) {
  "use strict";

$("#wrap-messages").sortable({
  handle: ".draggable", // Identifica el ícono como el área de arrastre
  axis: "y", // Limita el movimiento al eje vertical
  containment: "parent", // Limita el reordenamiento al contenedor
  placeholder: "sortable-placeholder", // Placeholder que ocupa el espacio
  start: function (event, ui) {// Ajustar el tamaño del placeholder dinámicamente al del elemento arrastrado
    ui.placeholder.height(ui.item.outerHeight());
  },
  update: function (event, ui) {// Callback que se ejecuta cuando el orden cambia
    //console.log(messageList()) 
  }
});

$(document).ready(function () {
  var firstMessage = $('#wrap-messages .message').first();
  var msgID=''
  if (firstMessage.length > 0) {
    msgID = firstMessage.attr('id');
  }

  $('#wrap-messages').on('click', '.message', function () {
    //console.log("click");
    msgID = $(this).attr('id');
    var content = html2Json($(this).find('p').html());
    $('[id^="delmsg_"]').attr('id', `delmsg_${msgID}`);
    $('#message-editor').val(content.trim());

   });

  $('#message-editor').on('input', function () {
    var content = $(this).val() 
    $(`#${msgID} p`).html(json2Html(content));
  });

  $('[id^="delmsg_"]').on('click', function (e) {
    e.preventDefault(); // Prevenir la acción del enlace
    var messageId = $(this).attr('id').replace('delmsg_', ''); // Resultado: "25"
    //console.log(messageId)
    $(`#${messageId}`).remove();
    updateEditorAfterDelete();
  });

  function updateEditorAfterDelete() {
    // Verificar si hay mensajes restantes
    var remainingMessages = $('#wrap-messages .message');
    if (remainingMessages.length > 0) {
      var firstMessage = remainingMessages.first();
      var msgID=firstMessage.attr('id')
      var content = html2Json(firstMessage.find('p').html())
      $('#message-editor').val(content.trim());
      $('[id^="delmsg_"]').attr('id', `delmsg_${msgID}`);
    } else {
      $('[id^="delmsg_"]').addClass('d-none')
      $('#message-editor').val('Debes añadir al menos un mensaje').prop('disabled', true);  // Deshabilitar el editor
    }
  }
});

// restore editor
$('#wrap-messages').on('DOMNodeInserted', function(e) {
     if ($('#message-editor').prop('disabled')) {
      $('#message-editor').prop('disabled', false);
      $('[id^="delmsg_"]').removeClass('d-none')

    var firstMSG = $('#wrap-messages .message').first();
    var msgID = firstMSG.attr('id');
    var content = html2Json(firstMSG.find('p').html());
    $('[id^="delmsg_"]').attr('id', `delmsg_${msgID}`);
    $('#message-editor').val(content.trim());

    }
  });

 

$('#keywords').on('keydown', function (e) {
        const key = e.key || e.code;
        if (e.key === 'Enter' || e.key === ','|| e.key === '13') {
            e.preventDefault();
            e.stopPropagation();
            const input = $(this);
            const value = input.val().trim().replace(',', '');
            if (value) {
                $('.tags').append(`
                    <span class="tag">
                        ${value}
                    <a href="#" class=" ms-2 remove-tag" ><i class="gicon-close"></i></a>
                    </span>
                `);
                input.val(''); // Limpiar el input
            }
        }
    });


$(document).on('click', '.remove-tag', function (e) {
    e.preventDefault()
    $(this).closest('.tag').remove();
});

$('#add_text_msg').on('click', function() {
  
    // Encuentra todos los mensajes existentes en #wrap-messages
    let maxId = 0;
    $('#wrap-messages .message').each(function() {
        let id = $(this).attr('id');
        if (id) {
            let num = parseInt(id.split('_')[1]); // Extrae el número después de "m_"
            if (num > maxId) {
                maxId = num; // Actualiza el máximo si es mayor
            }
        }
    });

    // Calcula el nuevo ID
    let newId = maxId + 1;

    // Construye el nuevo mensaje
    let newMessage = `
    <div class="message d-flex justify-content-between" id="m_${newId}" type="text">
        <div>
            <p>Edita este mensaje.</p> 
        </div>
        <i class="gicon-drag draggable ui-sortable-handle"></i>
    </div>
    `;

    // Añade el nuevo mensaje al final de #wrap-messages
    $('#wrap-messages').append(newMessage);
});



$('#flowEdit').on('keydown', function(event) {
       //event.preventDefault();
      //event.stopPropagation();
  
   /* if (event.key === 'Enter') {
      event.preventDefault();
      event.stopPropagation();
      var form = $('#flowEdit');
  if (form[0].checkValidity()) {
   
    //updateFlowName();
  } else {
    // Mostrar mensajes de validación
    var datas = {
      "flowEdit": {
        "valueMissing": "Por favor, ingresa un nombre para tu Flujo.",
        "patternMismatch": "El nombre debe ser mayor de 3 caracteres y menor de 50."
      },
    };
    validationFeedback(form, datas)
  }
  form.addClass('was-validated');
    }*/
});

/*$('#flow_name').on('focusout', function(event) {
   event.preventDefault();
    event.stopPropagation();
  var form = $('#flowEdit');
  if (form[0].checkValidity()) {
   //console.log("focusout")
    //updateFlowName();
  } else {
    // Mostrar mensajes de validación
    var datas = {
      "flow_name": {
        "valueMissing": "Por favor, ingresa un nombre para tu Flujo.",
        "patternMismatch": "El nombre debe ser mayor de 3 caracteres y menor de 50."
      },
    };
    validationFeedback(form, datas)
  }
  form.addClass('was-validated');
});*/

 
function updateFlowName(){
  var alertOptions = {icon: 'error', title:''};
   var formData = $('#flowEdit').serialize();
 
  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#submit_edit_flow', 'Guardar')
  formData= formData +'&action=updateFlowName';
  formData= formData +'&controller=admin/f/FController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      //showSpinner('#submit_edit_business', 'Guardar', false)
      if(response.status == 'success') {
        $('#submit_edit_bot').addClass('disabled')

         alertOptions.title='Nombre del Flujo actualizado.' 
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




$(document).on('click', '#delFLow', function(event) {
   event.preventDefault();
  var id = $('#flowEdit input[name="flowID"]').val();
  var name =$('#flow_name').val();

  var swalOptions = {
      icon: 'warning',
      title: '¡Elimiará el Flujo "' + name + '" de forma permanente!',
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
var formData = $('#flowEdit').serialize();
var botID=$('input[name="botID"]').val()
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&businessID='+botID+ '&flow_id='+id+'&action=delFlow&controller=admin/f/FController',
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') {    
      alertOptions.icon = 'success'
      alertOptions.title = 'Flujo eliminado.';
              ;
      alertToast(alertOptions);
      

      window.location.href = site_url+'admin/b/'+ botID;  
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


$('#submit_save_flow').on('click', function(event) {
   event.preventDefault();
   event.stopPropagation();

// Selecciona los spans dentro del div con clase 'tags'
const tagsArray = $('.tags .tag')
    .map(function () {
        // Obtén solo el texto dentro del span, excluyendo el contenido de los enlaces
        return $(this).contents()
            .filter(function () {
                return this.nodeType === Node.TEXT_NODE; // Solo nodos de texto
            })
            .text().trim(); // Limpia espacios en blanco
    })
    .get(); // Convierte el objeto jQuery en un array normal

// Convierte el array a JSON
var trigger_words = JSON.stringify(tagsArray);

var messages = JSON.stringify(messageList())
 console.log(messages)
var alertOptions = {icon: 'error', title:''};
const flowType = $('#flow_type').val();
if (messages=='[]') {
  var swalOptions = {
          icon: 'warning',
          title: 'Debes añadir al menos un mensaje.',
         };
swalAlert(swalOptions)
}
else {
  updateFlowDatas(flowType, trigger_words, messages);  

}
 });

function updateFlowDatas(flowType, trigger_words, messages){
var trigger_words = trigger_words || ""; 
var flowType = flowType || "1";
var alertOptions = {icon: 'error', title:''};
var formData = $('#flowEdit').serialize();
var botID=$('input[name="botID"]').val()
var flowID=$('input[name="flowID"]').val()
var flowName=$('#flow_name').val()

$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&botID='+botID+ '&flow_name='+flowName+ '&flow_id='+flowID+'&flowType='+flowType+'&trigger_words='+trigger_words+'&messages='+messages+'&action=updateFlowDatas&controller=admin/f/FController',
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') {    
      alertOptions.icon = 'success'
      alertOptions.title = 'Flujo actualizado.';
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










function messageList() { 
  
    let messages = [];
    // renovamos el ID de cada mensaje
    var id = 0;
    // Iterar solo sobre los mensajes dentro de .wrap-messages
    $('#wrap-messages .message').each(function () {
        let $message = $(this); // Referencia al mensaje actual
        // Leer y procesar el contenido del párrafo
        let content = html2Json($message.find('p').html()) 
       
        id++;
               // Agregar el mensaje procesado al array
        messages.push({
            id: id,
            type: 'text', // Asumimos que el tipo siempre es texto
            content: content.trim() // Eliminar espacios innecesarios
        });
    });

    return messages;
}



function json2Html(content){
   var htmlContent = content
  .replace(/\n/g,'<br>')           // Convertir \n a <br>
  .replace(/\*(.*?)\*/g, '<strong>$1</strong>') // Convertir *...* a <strong>
  .replace(/_(.*?)_/g, '<em>$1</em>')          // Convertir _..._ a <em>
  .replace(/~(.*?)~/g, '<del>$1</del>');       // Convertir ~...~ a <del>
  return htmlContent
}

function html2Json($content) {
  let response = $content
            .replace(/<br\s*\/?>\s*\n?/g, '\n')
            //.replace(/<br\s*\/?>/g, '\n')        // Convertir <br> en \n
            .replace(/<strong>(.*?)<\/strong>/g, '*$1*') // Convertir <strong> en *
            .replace(/<em>(.*?)<\/em>/g, '_$1_')         // Convertir <em> en _
            .replace(/<del>(.*?)<\/del>/g, '~$1~')       // Convertir <del> en ~
return response;
}


// add flow
$('#add_flow,#sAdd_flow').on('click', function(event) {
    event.preventDefault();
    registerFlow();
 });

function registerFlow(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#flowEdit').serialize();

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
          confirmButtonText:'Editar Flujo',
        };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url + 'admin/b/'+ response.bot_id+'/f/'+response.flow_id+'/'
          }  
        }); 
      }
      else { 
           if (response.message==='overPlan') {
          swalOptions = {
            icon: 'error',
            title: 'Lo sentimos...',
            html: '<p>Ha superado el límite de Flujos para este Bot. Debe actualizar el Bot a un plan superior.</p>',
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
          showSpinner('#submit_add_bot', 'Añadir Flujo', false)
          alertOptions.title=ajaxError(status, error).title 
          alertToast(alertOptions);
        }
      });
  
}


$("#edit_next").click(function() {
    editNext('next');
});

$("#edit_prev").click(function() {
    editNext('prev');
});

function editNext(direction){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#flowEdit').serialize();
  var botID=$('input[name="botID"]').val()

  /* Mostramos un loader hasta que se procese la solocitud */
  formData= formData +'&action=editNext&direction='+direction;
  formData= formData +'&controller=admin/f/FController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') {
        window.location.href = site_url + 'admin/b/'+ botID+'/f/'+response.next_id+'/'
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
          alertOptions.title=ajaxError(status, error).title 
          alertToast(alertOptions);
        }
      });
};


})(jQuery);


