<?php 

if ($_SESSION['userRole']!="admin") {
 $this->errorControl('Permissions', site_url.'admin/p/');
} 

$resp = $this->getDatas('getCurrencies') ;

if (isset($resp['status'])&&$resp['status']=='success') {
$currencies = $resp['rows'];
}
else{
  $currencies = [];
}

$resp = $this->getDatas('getWorkers') ;

if (isset($resp['status'])&&$resp['status']=='success') {
$workers = $resp['rows'];
}
else{
  $workers = [];
} 

 $resp = $this->getDatas('getGestors') ;

if (isset($resp['status'])&&$resp['status']=='success') {
$gestors = $resp['rows'];
}
else{
  $gestors = [];
}  
 //var_dump($resp);



 ?>


<div class="pagetitle ">
  <h1 class="me-3">Añadir Proyecto</h1>

</div><!-- End Page Title -->

<div class="container p-4 card my-3">
  <div class="row">
    <div class="col-12">
      <form id="addProject" class="needs-validation " novalidate>
        <div class="row">
          <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
          <input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">

          <div class="mb-3 col-12 col-md-6  ">
            <label class="form-label" for="project_name">Nombre del proyecto</label>
            <input class="form-control" type="text" name="project_name"   id="project_name" value=""  autofocus required placeholder="Marketing La Casona" pattern="^.{3,50}$">
            <div class="invalid-feedback validation_project_name"></div> 
          </div> 

          <div class="mb-3 col-6 col-md-3  ">
            <label for="start_date" class="form-label">Fecha de Inicio:</label>
            <input type="date" class="form-control" name="start_date" id="start_date">
          </div> 
          <div class="mb-3 col-6 col-md-3  ">
            <label for="end_date" class="form-label">Fecha de Finalización:</label>
            <input type="date" class="form-control" name="end_date" id="end_date">
            <div class="invalid-feedback validation_end_date"></div>
          </div> 

          <div class="mb-3 col-12 col-md-6  ">
              <label for="description" class="form-label">Descripción:</label>
              <textarea class="form-control" name="description" id="description" rows="4" required minlength="20" placeholder="Coloca aquí una descripción breve del proyecto y/o los datos de contacto del cliente"></textarea>
            <div class="invalid-feedback validation_description"></div> 
          </div> 
          
          <div class="mb-3 col-6 col-md-3  ">
            <label for="workers" class="form-label">Trabajadores:</label>            
            <?php if (!empty($workers)): ?>
            <select class="form-select" name="workers[]" id="workers" multiple>
              <?php foreach ($workers as $worker): ?>
              <option value="<?= $worker['worker_id'] ?>">
                <?= htmlspecialchars($worker['name']) ?> 
              </option>
            <?php endforeach; ?>
            </select>
            <div class="form-text">Mantén presionada Ctrl (Windows) o Cmd (Mac) para seleccionar varios.</div>
          <?php else: ?>
          <select class="form-select" disabled>
            <option>No hay trabajadores disponibles</option>
          </select>
          <div class="text-warning mt-1">Debes registrar al menos un trabajador para poder asignarlo al proyecto.</div>
        <?php endif; ?>
          </div> 

          <div class="mb-3 col-6 col-md-3  ">
            <label for="gestors" class="form-label">Gestores:</label>            
            <?php if (!empty($gestors)): ?>
            <select class="form-select" name="gestors[]" id="gestors" multiple>
              <?php foreach ($gestors as $gestor): ?>
              <option value="<?= $gestor['user_id'] ?>">
                <?= htmlspecialchars($gestor['email']) ?> 
              </option>
            <?php endforeach; ?>
            </select>
            <div class="form-text">Solo los usuarios con rol de gestor pueden ser asignados.</div>
          <?php else: ?>
          <select class="form-select" disabled>
            <option>No hay gestores disponibles</option>
          </select>
          <div class="text-warning mt-1">Debes crear al menos un usuario con rol de "Gestor" si deseas delegar este proyecto.</div>
        <?php endif; ?>
          </div>  
          <div class="mb-3 col-6 col-md-3  ">
            <label for="initial_amount" class="form-label">Monto inicial:</label>
              <input type="number" class="form-control" name="initial_amount" id="initial_amount" step="0.01" min="0" value="0.00">
            </div> 
            <div class="mb-3 col-6 col-md-2  ">
              <label for="initial_currency" class="form-label">Moneda:</label>
                <select name="initial_currency" id="initial_currency" class="form-select">
                <?php foreach ($currencies as $currency): ?>
                  <option value="<?= $currency['code'] ?>">
                    <?= $currency['code'] ?>
                  </option>
                    <?php endforeach; ?>
                </select>
            </div>   
        </div>
      </form>
      <button id="submit_add_project" class="btn btn-primary  ms-auto d-block my-3" >Añadir Proyecto</button>
    </div>
  </div>
</div>