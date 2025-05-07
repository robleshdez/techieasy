<div class="container-fluid  ">
  <div class="row">
    <aside id="sidebar" class="sidebar col">
      <?php  include 'app/views/admin/parts/aside.php';?>
      <?php //echo $adminSidebar; ?>
      <?php //echo $commonSidebar; ?>
    </aside>

    <main id="main" class="main col admin">
   <?php  include 'app/views/admin/parts/navbar.php';?>

  <?php //print_r($routeParams); ?>
  <section class="section dashboard pt-3">
    <div class="row">
      <?php echo $content; ?>
    </div>
  </section>
 </main><!-- End #main -->

  </div>
  

</div>

 