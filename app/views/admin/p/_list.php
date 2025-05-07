<div id="all_items" class="row mb-4">
<?php
   $projects = $response['projects'];
 if (!empty($projects)): ?>
<div class="card b-list-card p-3">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($projects as $project): 
        $project_code = 'PROY-' . str_pad($project['project_id'], 4, '0', STR_PAD_LEFT);
      ?>
      <tr>
        <td><?= htmlspecialchars($project_code) ?></td>
        <td><?= htmlspecialchars($project['name']) ?></td>
        <td><?= htmlspecialchars(substr($project['description'], 0, 50)) ?>...</td>
        <td>
          <a href="<?= site_url . 'admin/p/' . $project['project_id'] ?>" class="btn btn-sm btn-outline-primary">Ver</a>
          <a href="<?= site_url . 'admin/p/edit/' . $project['project_id'] ?>" class="btn btn-sm btn-outline-warning">Editar</a>
          <a id="del_<?= $project['project_id'] ?>" href="#" class="btn btn-sm btn-outline-danger">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php 
   if (isset($response['total_pages'])&& $response['total_pages']>1) { 
    pagination($response['total_pages'],$response['page']);
   }
?>
<?php else: ?>
<span id="noP">No tienes proyectos creados o asignados.</span>
<?php endif ?>


</div>
