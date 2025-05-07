!(function($) {
  "use strict";
 
 
  $(document).ready(function() {
    $('#start_date').on('change', function() {
      const startDate = $(this).val();
      $('#end_date').attr('min', startDate);
    });
  });
 
$('#submit_add_project').on('click', function(event) {
  var form = $('#addProject');
    if (form[0].checkValidity()) {
      addProject()
    } else {

    var datas = {
    "project_name": {
      "valueMissing": "Por favor, ingresa un nombre para el proyecto.",
      "patternMismatch": "El nombre debe ser mayor de 3 caracteres y menor de 50."
    },
    "description": {
      "valueMissing": "Por favor, ingresa la descripción del proyecto.",
      "tooShort": "La descripción debe ser mayor de 20 caracteres."
    },
     "end_date": {
      "rangeUnderflow": "No puede ser anterior a la fecha de inicio.'",
     },
};
validationFeedback(form, datas)
}
form.addClass('was-validated');
 
});


 

 

function addProject(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#addProject').serialize();

  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#submit_add_project', 'Añadiendo...')
  formData= formData +'&action=addProject';
  formData= formData +'&controller=admin/p/PController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      showSpinner('#submit_add_project', 'Añadir Proyecto', false)
      if(response.status == 'success') {
        var swalOptions = {
          icon: 'success',
          title: 'Proyecto añadido',
         };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url + 'admin/p/'
          }  
        }); 
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
          showSpinner('#submit_add_project', 'Añadir Proyecto', false)
          alertOptions.title=ajaxError(status, error).title 
          alertToast(alertOptions);
        }
      });

}
 
 

})(jQuery);


