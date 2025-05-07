!(function($) {
  "use strict";

// Asignar la función al callback de paginación global
fetchDataForPage = getProjectsForUser;

function getProjectsForUser(page) {
//showSpinner('#submit_register', 'Creando tu cuenta')
var alertOptions = {icon: 'error', title:''};
if (!page) {page=1}
  var formData = $('#tokens').serialize();
$.ajax({
  type: 'POST',
  url: site_url+'app/controllers/AjaxController.php',
  data: formData + '&page='+page+'&action=getProjectsForUser&controller=admin/p/PController',
  dataType: 'json',
  success: function(response) {
    if(response.status == 'success') { 
       $('#all_items').html(response.html);   
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

function listingBots(rows) {
  var rowBody = $('#all_items');

// Vaciar el contenido actual
if (rows.length !== 0) {
  rowBody.empty();
}
var html = $('<div class="card b-list-card p-3"></div>');
var table = $('<table class="table table-hover align-middle"></table>');
var thead = $('<thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>');
var tbody = $('<tbody></tbody>');
$.each(rows, function(index, row) {


// Crear la estructura de la tarjeta
var tr = $('<tr></tr>');
var td = $('<td></td>').text(row.project_code);
var td1 = $('<td></td>').text(row.name);
var td2 = $('<td></td>').text(row.description.substring(0, 50) + '...');
var actionHtml = `
  <a href="${site_url + 'admin/p/' + row.project_id}" class="btn btn-sm btn-outline-primary">Ver</a>
  <a href="${site_url + 'admin/p/edit/' + row.project_id}" class="btn btn-sm btn-outline-warning">Editar</a>
`;

if (row.userRole == 'admin') {
  actionHtml += ` <a id="del_${row.project_id}" href="#" class="btn btn-sm btn-outline-danger">Eliminar</a>`;
}

var td3 = $('<td></td>').html(actionHtml);

  

tr.append(td, td1, td2, td3);
tbody.append(tr);


});
// Agregar el enlace al contenedor principal
table.append(thead);
table.append(tbody);
html.append(table);
rowBody.append(html);
}






 

})(jQuery);


