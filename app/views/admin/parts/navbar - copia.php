<!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
     <div class="d-flex align-items-center justify-content-between ">
      <a href="<?php echo site_url ?>" class="logo d-flex align-items-end">
        <img src="<?php echo site_url . "public/img/favicon.png" ?>" alt="logo">
        <span class="d-none d-md-block">BeBots</span>
      </a>
      <i class="gicon-menu toggle-sidebar-btn d-block d-md-none"></i>
  </div><!-- End Logo -->
  

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

     
        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="gicon-info"></i> 
            <span class="badge bg-primary badge-number">4</span>
            
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end notifications ">
            <li class="dropdown-header">
              Tienes 4 nuevas notificaciones
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="notification-item">
              <a class="dropdown-item" href="#">
               <h4><i class="gicon-info text-warning"></i>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="notification-item">
              <a class="dropdown-item" href="#">
                <h4><i class="gicon-info text-primary"></i>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                </a>
            </li>
            
          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-item nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <span class="d-none d-md-block dropdown-toggle ps-2">
            <?php if (isset($_SESSION['userEmail'])){
            $userName = ucfirst(current(explode('@', $_SESSION['userEmail'])));
             echo 'Hola, '. $userName;
          }?></span>
          <span class="d-block d-md-none dropdown-toggle ps-2">
            <i class="gicon-admin"></i>
          </span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end profile ">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="gicon-config"></i><span>Mi cuenta</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
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