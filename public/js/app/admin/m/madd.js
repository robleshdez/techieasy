!(function($) {
  "use strict";

// add media
$('#drop-zone').on('dragover', function (e) {
  e.preventDefault();
  $('body').addClass('dragover');
});


$('#drop-zone').on('dragleave', function () {
  $('body').removeClass('dragover');
});

$('#drop-zone').on('drop', function (e) {
  e.preventDefault();
  $('body').removeClass('dragover');
  addFiles(e.originalEvent.dataTransfer.files);
});

// Agregar evento para el botón "Seleccionar Archivo"
$('#upload_media').on('click', function () {
  $('#file-input').click();
});
// Agregar evento cuando se selecciona un archivo
$('#file-input').on('change', function () {
  addFiles($(this)[0].files);
});

// Función para manejar los archivos seleccionados
function addFiles(files) {
  var alertOptions = {icon: 'error', title:''};
  var msg= (files.length>1)? 'Subiendo las imágenes':'Subiendo la imagen'
  var overPlan=false
 
  for (let i = 0; i < files.length; i++) {
    if (overPlan) {
    showSpinner('#upload_media', 'Selecciona la imagen', false)
    updateSpaceUsed(response)
    break;
    }
     
    const file = files[i];
    if (file) {
      if (file.type.match('image/jpeg') || file.type.match('image/png')) {
        if (file.size <= 1024 * 1024) { // 1 MB en bytes
          showSpinner('#upload_media', msg, true)
          // Crear objeto FormData para enviar el archivo al servidor
          const formData = new FormData();
          formData.append('file', file);
          // Agregar los datos adicionales al formData
          formData.append('csrfToken', $('#csrfToken').val());
          formData.append('csrfTimestamp', $('#csrfTimestamp').val());
          formData.append('businessID', $('#businessID').val());
          formData.append('action', 'addMedias');
          formData.append('controller', 'admin/m/MController');
          $.ajax({
            url: site_url+'app/controllers/AjaxController.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              showSpinner('#upload_media', 'Selecciona la imagen', false)
              if(response.status == 'success') {
              alertOptions.icon='success'
              alertOptions.title = 'El archivo <strong>'+file.name+'</strong> se ha guardado.';
              updateSpaceUsed(response)
              alertToast(alertOptions);
            }
            else {
              if (response.message==='noToken') {toErrorView('403')}
                else if (response.message==='overPlan') {
                  var swalOptions = {
                    icon: 'warning',
                    title: '¡Ha sobrepasado su plan!',
                    html: '<p>Ha sobrepasado el plan de espacio para este negocio. Actualice su plan.</p>',
                  };
                  overPlan=true;
                  swalAlert(swalOptions).then((result) => {
                    if (result.isConfirmed) {
                     updateSpaceUsed(response)
                    }  
                  });

                }
                else if (response.message==1045) {toErrorView('503')}
                else if (response.message=='notPermission') {toErrorView('Permissions')}// mejorar
                  else {
                    alertOptions.title = 'Ha ocurrido un error al intentar conectar con la base de datos.';
                    alertToast(alertOptions);
                  }
                }
              },

              error: function(xhr, status, error) {
                showSpinner('#upload_media', 'Selecciona la imagen', false)
                alertOptions.title=ajaxError(status, error).title 
                alertToast(alertOptions);
              }
            });
        }
        else {
          alertOptions.title = 'El archivo <strong>'+file.name+'</strong> es demasiado grande. Máximo 1 MB permitido.';
          alertToast(alertOptions);
        }
      } 
      else {
        var swalOptions = {
          icon: 'error',
          title: 'Formato de archivo no válido',
          html: '<p>Solo se permiten archivos JPG y PNG.</p>',
        };
        swalAlert(swalOptions)
        alertOptions.title = file.name + ' Tiene un formato no válido.Solo se permiten archivos JPG y PNG.';
        alertToast(alertOptions);
      }
    }
  }
   
}

function updateSpaceUsed(response){
  var totalSpace = response.total_space;
  var spacePlan=response.disk_capacity;
$('#spaceUsed').text(totalSpace + 'MB usados de ' + spacePlan + 'MB');
  }

 


})(jQuery);


