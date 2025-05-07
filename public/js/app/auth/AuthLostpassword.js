// AuthLostPaswword.js
// JS para validar y controlar por ajax las acciones de registro

!(function($) {
  "use strict";



//recuperar
$('#submit_recovery').on('click', function(event) {
 var form = $('#recovery');
 if (form[0].checkValidity()) {
   recoveryAcount();
 } else {
   event.preventDefault();
       // Mostrar mensajes de validación
         var datas = {
    "recovery_email": {
        "valueMissing": "Por favor, ingresa tu correo electrónico.",
        "patternMismatch": "Ingresa un correo electrónico válido."
    }
    
    
};
validationFeedback(form, datas)
 }
 form.addClass('was-validated');
});


function recoveryAcount(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#recovery').serialize(); // Obtener los datos del formulario
  var email=$('#recovery_email').val();
  showSpinner('#submit_recovery', 'Recuparando tu cuenta')

  $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&action=recoveryAcount&controller=auth/AuthController',
    
    dataType: 'json',
    success: function(response) {
      showSpinner('#submit_recovery', 'Recuperar', false)

      if(response.status == 'success') {
        var swalOptions = {
          icon: 'success',
          title: 'Recupera tu cuenta',
          html: '<p>Hemos enviado un correo electrónico a <strong>'+email+'</strong> con el enlace para recuperar tu cuenta. Si no lo encuentras, por favor, revisa en tu carpeta de spam.</p>',
        };
        
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            //location.reload();
            window.location.href = site_url +'login/';// Al login
          }  
        }); 
     } 
     else {
      if (response.message==='notExists') {
        var swalOptions = {
            icon: 'warning',
            title: 'Oops...',
            text: 'Parece que este correo no está registrado.',
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
       showSpinner('#submit_recovery', 'Recuperar', false)
       alertOptions.title = ajaxError(status, error).title
       alertToast(alertOptions);
     }


   });

}




})(jQuery);