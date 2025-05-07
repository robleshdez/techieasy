// AuthResetpassword.js
// JS para validar y controlar por ajax las acciones de registro

!(function($) {
  "use strict";

// Inicializamos valores
var toLogin = getNewURL('login')+'/login'
var urlParams = new URLSearchParams(window.location.search);
var rptoken = urlParams.get('rp');
if (rptoken) {
  $('#rpuser_token').val(rptoken)
}


$(window).on('load', function() {
  // Generamos contraseña aleatorea y actualziamos el MeterPassword
  $('#reset_password').val(generatePassword());
  updateMeterPassword($("#reset_password").val(),""); 
});


$('#submit_reset').on('click', function(event) {
 var form = $('#reset');
  if (!rptoken) {
    var swalOptions = {
           icon: 'error',
          title: 'Token no válido',
        };

        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            //location.reload();
            window.location.href = site_url + 'login/';// Al login
          }  
        }); 

  } else { 
   if (form[0].checkValidity() ) {
       reset_pasw();
   } else {
       event.preventDefault() 
       event.stopPropagation();
      window.location.href = toLogin;// Al login
   }
   form.addClass('was-validated');
   }
});

 function reset_pasw(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#reset').serialize(); // Obtener los datos del formulario
   showSpinner('#submit_reset', 'Estableciendo contraseña')
     $.ajax({
      type: 'POST',
      url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&action=resetPassword&controller=auth/AuthController',
      dataType: 'json',
      success: function(response) {
        showSpinner('#submit_reset', 'Establecer', false)
        if(response.status == 'success') {
         var swalOptions = {
          icon: 'success',
          title: 'Contraseña restablecida',
          text: 'Su contraseña ha sido restablecida con éxito',
        };

        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            //location.reload();
            window.location.href = site_url + 'login/';// Al login
          }  
        }); 
        } 
        else {
            if (response.message==='invalidToken') {
            swalOptions = {
          icon: 'error',
          title: 'Token no válido',
         };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            //location.reload();
           window.location.href = toLogin;// Al login
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
      showSpinner('#submit_reset', 'Establecer', false)
      alertOptions.title = ajaxError(status, error).title
      alertToast(alertOptions);
    }
  
    });

 }


$('#reset_password').on('keyup focusout focus', function() {
  updateMeterPassword($("#reset_password").val(),"")
  var extensionValida = passwordValidate($("#reset_password").val(),"");
   if (!extensionValida) {
    this.setCustomValidity('Por favor, contraseña');
  } else {
    this.setCustomValidity('');
  }
})

showPassword('.pswd', '.showPassword')

})(jQuery);
