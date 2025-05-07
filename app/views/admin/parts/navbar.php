<!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
     <div class="d-flex align-items-center justify-content-between ">
      <a href="<?php echo site_url ?>" class="logo d-flex align-items-center">
        <img src="<?php echo site_url . "public/img/favicon.png" ?>" alt="logo">
        <span class="d-none d-md-block">Botzy</span>
      </a>
      <i class="gicon-menu toggle-sidebar-btn d-block d-md-none"></i>
  </div><!-- End Logo -->
  

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">

          <a class="nav-item nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <span class="d-none d-md-block dropdown-toggle ps-2">
                <i class="gicon-user"></i>
            <?php if (isset($_SESSION['userEmail'])){
            $userName = ucfirst(current(explode('@', $_SESSION['userEmail'])));
             echo 'Hola, '. $userName;
          }?></span>
          
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end profile ">
             
             
             <li class="dropdown-footer">
              <a id="mlogout" class="dropdown-item d-flex align-items-center" href="#">
                <i class="gicon-exit"></i><span>Salir</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->
         

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->