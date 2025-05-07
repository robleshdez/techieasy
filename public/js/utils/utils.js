// utils.js
// varias funciones facilitadoras

// devuelve la url hasta la parte marcada
function getNewURL(ixOF) {
	var currentURL = window.location.href;
  var cleanUrl = currentURL.split('?')[0];
// Divide la URL en partes usando la barra diagonal como separador
var parts = cleanUrl.split('/');
// Reconstruye la URL hasta /ixOF/
var newUrl = parts.slice(0, parts.indexOf(ixOF) ).join('/');
return newUrl
}

// Carga la página de error en la vista actual
function toErrorView(errorType, tolink='' , infoMsg='') {  
	$.ajax({
		type: 'POST',
		url: '../index.php',
		data: 'errorType=' + errorType+'&tolink=' + tolink+'&infoMsg=' + infoMsg,
		dataType:'html',
		//cache: false,
		success: function(response) 
                {// Actualiza el contenido en el cliente
                	document.open();
                	document.write(response);
                	document.close();

                }
              });
}

// Procesa los errores ajax
function ajaxError(status, error){

  if (status == 'timeout') {
    toAlertToast = {title: 'La solicitud ha tardado demasiado en responder.'};
  } else if (status == 'error') {
    toAlertToast = {title: 'Se ha producido un error en la solicitud: ' + error};
  } else if (status == 'abort') {
    toAlertToast = {title: 'La solicitud ha sido cancelada.'};
  } else if (status == 'parsererror') {
    toAlertToast = {title: 'No se puede analizar la respuesta JSON.'};
  } else {
    toAlertToast = {title: 'Ha ocurrido un error desconocido: ' + error};
  }
  return toAlertToast     
}

/* feedback de validación de los formularios*/
function validationFeedback(form, datas) {
    // Mostrar mensajes de validación para campos no válidos
    var invalidItems = form.find(':invalid');
    invalidItems.each(function() {
        // Personaliza los mensajes de validación aquí
        var fieldName = this.id;
        var errorMessage = '';

        // Verificar si hay mensajes de validación definidos para este campo en "datas"
        if (datas.hasOwnProperty(fieldName)) {
          var validations = datas[fieldName];
          

            // Verificar cada tipo de validación
            if (this.validity.valueMissing && validations.hasOwnProperty('valueMissing')) {
                errorMessage = validations.valueMissing;
            } else if (this.validity.tooShort && validations.hasOwnProperty('tooShort')) {
                errorMessage = validations.tooShort;
            } else if (this.validity.patternMismatch && validations.hasOwnProperty('patternMismatch')) {
                errorMessage = validations.patternMismatch;
            } else if (this.validity.typeMismatch && validations.hasOwnProperty('typeMismatch')) {
              errorMessage = validations.typeMismatch;
            } else if (this.validity.rangeUnderflow && validations.hasOwnProperty('rangeUnderflow')) {
              errorMessage = validations.rangeUnderflow;
            } else if (this.validity.rangeOverflow && validations.hasOwnProperty('rangeOverflow')) {
              errorMessage = validations.rangeOverflow;
            } else if (this.validity.stepMismatch && validations.hasOwnProperty('stepMismatch')) {
              errorMessage = validations.stepMismatch;
            } else if (this.validity.badInput && validations.hasOwnProperty('badInput')) {
              errorMessage = validations.badInput;
            } else if (this.validity.customError && validations.hasOwnProperty('customError')) {
              errorMessage = validations.customError;
            }
            
              //console.log (this.validity)
          } 
        // Mostrar el mensaje de error si está definido
        if (errorMessage !== '') {
          $('.validation_' + fieldName).html(errorMessage);
          //$('.validation_' + fieldName).addClass("d-block");
        }
        else {
          $('.validation_' + fieldName).removeClass("d-block");
        }
      });
  }


function stringToURL(texto, typing=true) {
    return texto.toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^\wñ\s-]+/g, '')  // Elimina caracteres no alfanuméricos, ni espacios ni guiones excepto los que están en el medio
        .replace(/ñ/g, 'n')
        .replace(/\s+/g, '-')  // Reemplazar espacios consecutivos con un solo guion
        .replace(/-$/, typing ? '-' : '');  // Eliminar el último guion solo si no estamos escribiendo
      }


function randomNameGen(prefix) {
    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; // Caracteres permitidos
    let resultado = prefix+'_'; // Inicia con 'Bot_'
    
    for (let i = 0; i < 6; i++) {
        const indice = Math.floor(Math.random() * caracteres.length);
        resultado += caracteres.charAt(indice); // Agrega un carácter aleatorio
    }
    
    return resultado; // Retorna el nombre generado
}


/*pagination*/

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


let fetchDataForPage = null; // Esto se sobreescribirá en cada módulo

//Add short link
$(document).on("click", ".linkeable", function(event){
  event.preventDefault();
  var numPage=$(this).text();
  if (!isNaN(numPage)) {
    fetchDataForPage(parseInt(numPage));
      } 
});


$(document).on("click", ".next", function(event){
  event.preventDefault();
  var numPage=  parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage+1
  if (fetchDataForPage) {
    fetchDataForPage(numPage);
  }
});

$(document).on("click", ".prev", function(event){
  event.preventDefault();
  var numPage= parseInt($("#all_items_pagination li.active span.page-link").text())
  numPage=numPage-1
  if (fetchDataForPage) {
    fetchDataForPage(numPage);
  }
});


function getDatasByPage(numPage, callback) {
    callback(numPage)
  } 
