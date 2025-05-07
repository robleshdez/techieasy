// AuthLogout.js
// JS gestionar el logout, 
// este js debe estar cargdo desde metas conttoller ya q se usará
// en todo el proyecto

!(function($) {
  "use strict";


//logout

var location = window.location.href;;
var redirectURL = location.replace(site_url, "");

$('#mlogout,#slogout').on('click', function(event) {
  event.preventDefault();
    var swalOptions = {
          icon: 'warning',
          title: '¿Seguro que desea salir?',
          confirmButtonText:'Sí, deseo salir',
          cancelButtonText:'No, regresar',
          showCancelButton: true,

         };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
           logout() 
          }  
        });
 })

function logout(){
  var alertOptions;
  alertOptions = {icon: 'error', title: ''};
  $.ajax({
    type: 'POST',
    url: site_url+'app/controllers/AjaxController.php',
    data: 'action=logout&controller=auth/AuthController',
    dataType: 'json',
    success: function(response) {
      if(response.status == 'success') {
       window.location.href = site_url+'login/'; 
       } 
     else {
      }
   },
   error: function(xhr, status, error) {
      alertOptions.title = ajaxError(status, error).title
      alertToast(alertOptions);
  }
});
}

// control de inactividad
setInterval(function() { 
  var alertOptions;
  alertOptions = {icon: 'error', title: ''};
  $.ajax({
   type: 'POST',
   url: site_url+'app/controllers/AjaxController.php',
   data: 'action=checkSesion&controller=auth/AuthController',
   dataType: 'json',
   success: function(response) {
    if (response == 'expired') {
         var swalOptions = {
          icon: 'info',
          title: 'Su sesión se ha cerrado por inactividad',
          confirmButtonText:'Acceder',
          allowOutsideClick: false,
         };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {      
            window.location.href = site_url+'login?rd='+redirectURL;  //aqui
          }  
        });

   }
 },
 error: function(xhr, status, error) {
   alertOptions.title = ajaxError(status, error).title
      alertToast(alertOptions);
}
});
}, 61 * 1000);




})(jQuery);


