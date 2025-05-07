!(function($) {
  "use strict";

// add negocio
$('#submit_add_business').on('click', function(event) {
  var form = $('#addBusiness');
  if (form[0].checkValidity()) {
    registerBusiness();
  } else {
    event.preventDefault();
    // Mostrar mensajes de validación
    var datas = {
      "business_name": {
        "valueMissing": "Por favor, ingresa el nombre de tu Bot.",
        "patternMismatch": "El nombre debe ser mayor de 3 caracteres y menor de 50."
      },
        "business_category": {
        "valueMissing": "Por favor, selecciona la categoria de tu negocio.",
      }
    };
    validationFeedback(form, datas)
  }
  form.addClass('was-validated');
});


 

$('#business_category').on('change', function() {
  var selectedValue = $(this).val();
  $('#business_category_val').val(selectedValue);
});


function registerBusiness(){
  var alertOptions = {icon: 'error', title:''};
  var formData = $('#addBusiness').serialize();
  var alias = $('#business_alias').val();

  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#submit_add_business', 'Creando tu Bot')
  formData= formData +'&action=addBusiness';
  formData= formData +'&controller=admin/b/BController';
  $.ajax({
    url: site_url+'app/controllers/AjaxController.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      showSpinner('#submit_add_business', 'Añadir Bot', false)
      if(response.status == 'success') {
        var swalOptions = {
          icon: 'success',
          title: '¡Felicidades! Ya se ha creado tu Bot',
          html: '<p>Edítalo para ponerlo online.</p>',
          confirmButtonText:'Editar Bot',
        };
        swalAlert(swalOptions).then((result) => {
          if (result.isConfirmed) {
            window.location.href = site_url + 'admin/b/'+ response.business_id+'/'
          }  
        }); 
      }
      else { 
          if (response.message==='businessExists') {
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
            html: '<p>Ha superado el límite de Bots gratis. Para registrar otro negocio, por favor, actualice su negocio con plan básico a un plan superior o compre la capacidad para un negocio con plan superior.</p>',
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
          showSpinner('#submit_add_business', 'Añadir negocio', false)
          alertOptions.title=ajaxError(status, error).title 
          alertToast(alertOptions);
        }
      });
  
}

$('#business_category').change(function() {
    var selectedCategoryId = $(this).val();
    $('#category_description li').addClass('d-none');
     if (selectedCategoryId !== '') {
      $('#' + selectedCategoryId).removeClass('d-none');
    }
  });



})(jQuery);


