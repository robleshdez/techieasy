// AuthLogin.js
// JS gestionar el login, la validación de cuenta 
// y recuperacion de contraseña

!(function($) {
  "use strict";

// Inicializamos valores

var urlParams = new URLSearchParams(window.location.search);
var validateToken = urlParams.get('v');
var redirectToken = urlParams.get('rd');
if (validateToken) {
   validateAcount(validateToken)
}
var redirect=redirectToken!=null?site_url+redirectToken:site_url+'admin/'
//console.log(redirect)
//Login
$('#submit_login').on('click', function(event) {
 var form = $('#login');
   if (form[0].checkValidity()) {
       login();
   } else {
       event.preventDefault();
       // Mostrar mensajes de validación
         var datas = {
    "login_email": {
        "valueMissing": "Por favor, ingresa tu correo electrónico.",
        "patternMismatch": "Ingresa un correo electrónico válido."
    },
    "login_password": {
        "valueMissing": "Por favor, no olvides tu contraseña."
    }
    
};
validationFeedback(form, datas)
   }
   form.addClass('was-validated');
});

 
function login(){
  var alertOptions, swalOptions;
  alertOptions = swalOptions = {icon: 'error', title: ''};
  var formData = $('#login').serialize(); // Obtener los datos del formulario
 
   $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&action=login&controller=auth/AuthController',
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') {
        window.location.href = redirect;  
      } 
      else {
        if (response.message==='invalidUser') {
          swalOptions.title = 'Usuario o contraseña incorrecta.';
        swalAlert(swalOptions)
        }
          else if (response.message==='suspendedAcount') {
            swalOptions.title = 'Su cuenta ha sido suspendida.';
            swalAlert(swalOptions)
            }
           else if (response.message==='unverifyAcount') {
            swalOptions.title = 'Cuenta sin verificar';
            swalOptions.html = '<p>No has verificado tu cuenta. Por favor, revisa tu correo para completar el proceso de verificación. Si no lo encuentras puedes <a id="verifyAcount" href="#">solicitar uno nuevo.</a></p>';
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
      alertOptions.title = ajaxError(status, error).title
      alertToast(alertOptions);
    }
  });
}

//Reenviar el correo de verificación
$(document).on('click', '#verifyAcount', function(event) {
 event.preventDefault();
 var form = $('#login');
   if (form[0].checkValidity()) {
       verifyAcount();
   } else {
       event.preventDefault();
       event.stopPropagation();
   }
   form.addClass('was-validated');
});

function verifyAcount(){
  var alertOptions, swalOptions;
  alertOptions = swalOptions = {icon: 'error', title: ''};
  var formData = $('#login').serialize(); // Obtener los datos del formulario
  var email = $('#login_email').val();
  showSpinner(true)
 
   $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&action=verifyAcount&controller=auth/AuthController',
    dataType: 'json',
    success: function(response) {
       showSpinner(false)
     if(response.status == 'success') {
      
        var swalOptions = {
          icon: 'success',
          title: '¡Por favor revisa tu correo electrónico!',
          html: '<p>Hemos enviado un correo electrónico a <strong>'+email+'</strong> con el enlace para validar tu cuenta. Si no lo encuentras revisa en tu carpeta de spam.</p>',
        };

        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url+'login/'
          }  
        }); 
      } 
      else {
        if (response.message==='invalidUser') {
          swalOptions.title = 'Usuario incorrecto.';
        swalAlert(swalOptions)
        }
          else if (response.message==='suspendedAcount') {
            swalOptions.title = 'Su cuenta ha sido suspendida.';
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
      showSpinner(false)
      alertOptions.title = ajaxError(status, error).title
      alertToast(alertOptions);
    }
  });
}

/*validar la cuenta*/
function validateAcount(validateToken){
  var alertOptions, swalOptions;
  alertOptions = swalOptions = {icon: 'error', title: ''};

  $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: 'vtoken='+validateToken+ '&action=validateAcount&controller=auth/AuthController',
    
    dataType: 'json',
    success: function(response) {
      //showLoader(false)
      if(response.status == 'success') {
         swalOptions = {
          icon: 'success',
          title: 'Su cuenta ha sido verificada',
          confirmButtonText: 'Acceder',
        };

          swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url+'login/';// Al login
          }  
        }); 
      } 
      else {
        if (response.message==='invalidToken') {
           swalOptions = {
          icon: 'error',
          title: 'Token no válido',
        }
           swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url+'login/';// Al login
          }  
        }); 
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
      //showLoader(false)
      alertOptions.title = ajaxError(status, error).title
      alertToast(alertOptions);
    }
  });
}


showPassword('.pswd', '.showPassword')

})(jQuery);