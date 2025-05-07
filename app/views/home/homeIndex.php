<!-- app/views/home/homeIndex.php -->

 <?php 

$response = $this->getDatas('getHomeStat') ;
$users=30;
$bots=25;
$flows=30;
if ($response&&$response['status']=='success') {
  $users=$response['total_users'];
$bots=$response['total_bots'];
$flows=$response['total_flows'];
} 

  ?>  
 <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
 
       <div class="d-flex align-items-center justify-content-between ">
      <a href="#hero" class="logo d-flex align-items-center">
        <img src="<?php echo site_url . "public/img/favicon.png" ?>" alt="logo">
        <span class="d-none d-md-block">Botzy</span>
      </a>
  </div><!-- End Logo -->
  

      <nav id="navmenu" class="navmenu">
        <ul> <li><a href="#hero" class="active">Inicio</a></li>
           <li><a href="#features">Beneficios</a></li>
          <li><a href="#faq">Preguntas frecuentes</a></li>
          <li><a href="#contact">Contáctanos</a></li>
         
        </ul>
        <i class="mobile-nav-toggle d-block d-md-none gicon-menu"></i>
       </nav>
         
          <div>
          <?php if (!isset($_SESSION['userID'])):?>
            <a href="<?php echo site_url.'login'; ?>" >Acceder</a>
           <a href="<?php echo site_url.'login/register'; ?>" class="btn-getstarted">Crea tu Bot gratis</a>
            <?php else: ?> 
            <a href="<?php echo site_url.'admin'; ?>" class="btn-getstarted">Acceder</a>

            <?php endif ?>
        </div>
          
           
        
 
    </div>
  </header>




<!-- Hero Section -->

    <section id="hero" class="hero section">
      <div class="wave">

      <svg width="100%" height="355px" viewBox="0 0 1920 355" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <g id="Apple-TV" transform="translate(0.000000, -402.000000)" fill="#FFFFFF">
            <path d="M0,439.134243 C175.04074,464.89273 327.944386,477.771974 458.710937,477.771974 C654.860765,477.771974 870.645295,442.632362 1205.9828,410.192501 C1429.54114,388.565926 1667.54687,411.092417 1920,477.771974 L1920,757 L1017.15166,757 L0,757 L0,439.134243 Z" id="Path"></path>
          </g>
        </g>
      </svg>

    </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
              <h1 class="mb-4">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                 
              </h1>
              <p class="mb-3 mb-md-4">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit.
              </p>
              <div class="hero-buttons">
                <a href="<?php echo site_url.'login/register'; ?>" class="btn btn-primary me-0 me-sm-2 mx-1">Lorem ipsum dolor</a>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
              <img src="<?php echo site_url.'public/img/home/hero.png' ?>" alt="Hero Image" class="img-fluid">
            </div>
          </div>
        </div>


         
 

      </div>

    </section><!-- /Hero Section -->

    <!-- Services Section -->
    <section id="features" class="services section ">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Transforma tu Negocio</h2>
        <p>Olvídate de perder tiempo respondiendo mensajes. <br>Con Botzy puedes ayudar a tus clientes, ¡incluso mientras duermes!</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
             <div class="service-card row">
              <div class="   col-12 col-md-2 mb-3">
                <div class="icon"> <i class="gicon-check"></i></div>
              </div>
              <div class="col-12 col-md-10">
                <h3 class="text-center text-md-start">Fácil de usar, sin conocimientos técnicos</h3>
                <p>Diseña tu bot con una interfaz amigable. Todo lo que necesitas es registrarte, personalizar respuestas y conectar tu WhatsApp.</p>
                 
              </div>
            </div>
          </div><!-- End Service Card -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-card row">
              <div class="   col-12 col-md-2 mb-3">
                <div class="icon"> <i class="gicon-check"></i></div>
              </div>
              <div class="col-12 col-md-10">
                <h3 class="text-center text-md-start">Conversaciones dinámicas </h3>
                <p>Crea flujos de conversaciones que responden a palabras clave y guían a tus clientes a soluciones rápidas y efectivas. Configura varias respuestas por flujo para mayor personalización.</p>
              </div>
            </div>
          </div><!-- End Service Card -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-card row">
              <div class="   col-12 col-md-2 mb-3">
                <div class="icon"> <i class="gicon-check"></i></div>
              </div>
              <div class="col-12 col-md-10">
                <h3 class="text-center text-md-start">Optimiza tu tiempo y recursos</h3>
                <p>Dedica menos tiempo a las tareas repetitivas. Deja que tu bot maneje la atención al cliente y convierta cada interacción en una oportunidad de venta.</p>
              
              </div>
            </div>
          </div><!-- End Service Card -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-card row">
              <div class="   col-12 col-md-2 mb-3">
                <div class="icon"> <i class="gicon-check"></i></div>
              </div>
              <div class="col-12 col-md-10">
                <h3 class="text-center text-md-start">Múltiples plataforma (próximamente)</h3>
                <p>Comienza con WhatsApp y, muy pronto, conecta tu bot a Telegram y otras plataformas para expandir tu alcance.</p>
              </div>
            </div>
          </div><!-- End Service Card -->

        </div>

      </div>

    </section><!-- /Services Section -->
<!-- Call To Action 2 Section -->
    <section id="call-to-action-2" class="call-to-action-2  ">

      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-10">
            <div class="text-center">
              <h2>Empieza a automatizar tu negocio hoy mismo</h2>
              <p>Crea tu chatbot en minutos, conecta con tus clientes y aumenta tus ventas sin complicaciones. Es fácil, rápido y gratis.</p>
              <a class="cta-btn" href="<?php echo site_url.'login/register'; ?>">Regístrate Gratis</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Call To Action 2 Section -->
     

    <!-- About Section -->
    <section id="about" class="about section pt-0 d-none">

        <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Transformar tu Negocio</h2>
        <p>Olvídate de perder tiempo respondiendo mensajes. <br>Con Botzy puedes ayudar a tus clientes, ¡incluso mientras duermes!</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4 align-items-center justify-content-between">

          <div class="col-xl-5" data-aos="fade-up" data-aos-delay="200">
             <h2 class="about-title">Voluptas enim suscipit temporibus</h2>
            <p class="about-description">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>

            <div class="row feature-list-wrapper">
              <div class="col-md-6">
                <ul class="feature-list">
                  <li><i class="bi bi-check-circle-fill"></i> Lorem ipsum dolor sit amet</li>
                  <li><i class="bi bi-check-circle-fill"></i> Consectetur adipiscing elit</li>
                  <li><i class="bi bi-check-circle-fill"></i> Sed do eiusmod tempor</li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="feature-list">
                  <li><i class="bi bi-check-circle-fill"></i> Incididunt ut labore et</li>
                  <li><i class="bi bi-check-circle-fill"></i> Dolore magna aliqua</li>
                  <li><i class="bi bi-check-circle-fill"></i> Ut enim ad minim veniam</li>
                </ul>
              </div>
            </div>

           
          </div>

          <div class="col-xl-6" data-aos="fade-up" data-aos-delay="300">
            <div class="image-wrapper">
              <div class="images position-relative" data-aos="zoom-out" data-aos-delay="400">
                <img src="assets/img/about-5.webp" alt="Business Meeting" class="img-fluid main-image rounded-4">
                <img src="assets/img/about-2.webp" alt="Team Discussion" class="img-fluid small-image rounded-4">
              </div>
              <div class="experience-badge floating">
                <h3>15+ <span>Years</span></h3>
                <p>Of experience in business service</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

     
     
   
    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section light-background d-none">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row g-5">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="testimonial-item">
              <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
              <h3>Saul Goodman</h3>
              <h4>Ceo &amp; Founder</h4>
              <div class="stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
              </div>
              <p>
                <i class="bi bi-quote quote-icon-left"></i>
                <span>Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.</span>
                <i class="bi bi-quote quote-icon-right"></i>
              </p>
            </div>
          </div><!-- End testimonial item -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="testimonial-item">
              <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
              <h3>Sara Wilsson</h3>
              <h4>Designer</h4>
              <div class="stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
              </div>
              <p>
                <i class="bi bi-quote quote-icon-left"></i>
                <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.</span>
                <i class="bi bi-quote quote-icon-right"></i>
              </p>
            </div>
          </div><!-- End testimonial item -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="testimonial-item">
              <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
              <h3>Jena Karlis</h3>
              <h4>Store Owner</h4>
              <div class="stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
              </div>
              <p>
                <i class="bi bi-quote quote-icon-left"></i>
                <span>Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.</span>
                <i class="bi bi-quote quote-icon-right"></i>
              </p>
            </div>
          </div><!-- End testimonial item -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="testimonial-item">
              <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
              <h3>Matt Brandon</h3>
              <h4>Freelancer</h4>
              <div class="stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
              </div>
              <p>
                <i class="bi bi-quote quote-icon-left"></i>
                <span>Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.</span>
                <i class="bi bi-quote quote-icon-right"></i>
              </p>
            </div>
          </div><!-- End testimonial item -->

        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Stats Section -->
    <section id="stats" class="stats section d-none">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
              <p>Usuarios</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
              <p>Bots</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
              <p>Hours Of Support</p>
            </div>
          </div><!-- End Stats Item -->

          

        </div>

      </div>

    </section><!-- /Stats Section -->

    
    <!-- Pricing Section -->
    <!-- <section id="pricing" class="pricing section light-background">

        
      <div class="container section-title" data-aos="fade-up">
        <h2>Pricing</h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
      </div> 

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4 justify-content-center">

           
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="pricing-card">
              <h3>Basic Plan</h3>
              <div class="price">
                <span class="currency">$</span>
                <span class="amount">9.9</span>
                <span class="period">/ month</span>
              </div>
              <p class="description">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium totam.</p>

              <h4>Featured Included:</h4>
              <ul class="features-list">
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Duis aute irure dolor
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Excepteur sint occaecat
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Nemo enim ipsam voluptatem
                </li>
              </ul>

              <a href="#" class="btn btn-primary">
                Buy Now
                <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

          
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="pricing-card popular">
              <div class="popular-badge">Most Popular</div>
              <h3>Standard Plan</h3>
              <div class="price">
                <span class="currency">$</span>
                <span class="amount">19.9</span>
                <span class="period">/ month</span>
              </div>
              <p class="description">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum.</p>

              <h4>Featured Included:</h4>
              <ul class="features-list">
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Lorem ipsum dolor sit amet
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Consectetur adipiscing elit
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Sed do eiusmod tempor
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Ut labore et dolore magna
                </li>
              </ul>

              <a href="#" class="btn btn-light">
                Buy Now
                <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

           
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="pricing-card">
              <h3>Premium Plan</h3>
              <div class="price">
                <span class="currency">$</span>
                <span class="amount">39.9</span>
                <span class="period">/ month</span>
              </div>
              <p class="description">Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae.</p>

              <h4>Featured Included:</h4>
              <ul class="features-list">
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Temporibus autem quibusdam
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Saepe eveniet ut et voluptates
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Nam libero tempore soluta
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Cumque nihil impedit quo
                </li>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  Maxime placeat facere possimus
                </li>
              </ul>

              <a href="#" class="btn btn-primary">
                Buy Now
                <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

        </div>

      </div>

    </section>/Pricing Section -->

    <!-- Faq Section -->
    <section class="faq-9 faq section light-background" id="faq">

      <div class="container">
        <div class="row">

          <div class="col-lg-5" data-aos="fade-up">
            <h2 class="faq-title text-center text-md-start">¿Tienes alguna duda?</h2>
            <p class="faq-description text-center text-md-start">Consulte las preguntas frecuentes</p>
            <div class="faq-arrow d-none d-lg-block" data-aos="fade-up" data-aos-delay="200">
              <svg class="faq-arrow" width="200" height="211" viewBox="0 0 200 211" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M198.804 194.488C189.279 189.596 179.529 185.52 169.407 182.07L169.384 182.049C169.227 181.994 169.07 181.939 168.912 181.884C166.669 181.139 165.906 184.546 167.669 185.615C174.053 189.473 182.761 191.837 189.146 195.695C156.603 195.912 119.781 196.591 91.266 179.049C62.5221 161.368 48.1094 130.695 56.934 98.891C84.5539 98.7247 112.556 84.0176 129.508 62.667C136.396 53.9724 146.193 35.1448 129.773 30.2717C114.292 25.6624 93.7109 41.8875 83.1971 51.3147C70.1109 63.039 59.63 78.433 54.2039 95.0087C52.1221 94.9842 50.0776 94.8683 48.0703 94.6608C30.1803 92.8027 11.2197 83.6338 5.44902 65.1074C-1.88449 41.5699 14.4994 19.0183 27.9202 1.56641C28.6411 0.625793 27.2862 -0.561638 26.5419 0.358501C13.4588 16.4098 -0.221091 34.5242 0.896608 56.5659C1.8218 74.6941 14.221 87.9401 30.4121 94.2058C37.7076 97.0203 45.3454 98.5003 53.0334 98.8449C47.8679 117.532 49.2961 137.487 60.7729 155.283C87.7615 197.081 139.616 201.147 184.786 201.155L174.332 206.827C172.119 208.033 174.345 211.287 176.537 210.105C182.06 207.125 187.582 204.122 193.084 201.144C193.346 201.147 195.161 199.887 195.423 199.868C197.08 198.548 193.084 201.144 195.528 199.81C196.688 199.192 197.846 198.552 199.006 197.935C200.397 197.167 200.007 195.087 198.804 194.488ZM60.8213 88.0427C67.6894 72.648 78.8538 59.1566 92.1207 49.0388C98.8475 43.9065 106.334 39.2953 114.188 36.1439C117.295 34.8947 120.798 33.6609 124.168 33.635C134.365 33.5511 136.354 42.9911 132.638 51.031C120.47 77.4222 86.8639 93.9837 58.0983 94.9666C58.8971 92.6666 59.783 90.3603 60.8213 88.0427Z" fill="currentColor"></path>
              </svg>
            </div>
          </div>

          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="300">
            <div class="faq-container">

              <div class="faq-item faq-active">
                <h3>¿Qué es Botzy?</h3>
                <div class="faq-content">
                  <p>Botzy es una plataforma que te permite crear chatbots para automatizar conversaciones en WhatsApp y próximamente en otras plataformas como Telegram. Es ideal para emprendedores y negocios que desean ahorrar tiempo, capturar clientes y aumentar sus ventas.</p>
                </div>
                <i class="faq-toggle gicon-arrowr"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>¿Necesito experiencia técnica para usar Botzy?</h3>
                <div class="faq-content">
                  <p>¡No! Botzy está diseñado para ser intuitivo y fácil de usar. En cuestión de minutos y con unos pocos clics, puedes crear tu bot, personalizar flujos de conversación y conectarlo a WhatsApp sin necesidad de conocimientos en programación.</p>
                </div>
                <i class="faq-toggle gicon-arrowr"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>¿Botzy funciona con otras plataformas además de WhatsApp?</h3>
                <div class="faq-content">
                  <p>Actualmente, Botzy está optimizado para WhatsApp, pero estamos trabajando para integrar Telegram y otras plataformas de mensajería próximamente.</p>
                </div>
                <i class="faq-toggle gicon-arrowr"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>¿Es gratis usar Botzy?</h3>
                <div class="faq-content">
                  <p>Sí, siempre tendrás acceso a un bot gratis con características básicas. También incluiremos otros planes para acceder a funcionalidades avanzadas.</p>
                </div>
                <i class="faq-toggle gicon-arrowr"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>¿Qué tipo de negocios pueden usar Botzy?</h3>
                <div class="faq-content">
                  <p>Cualquier negocio que interactúe con clientes a través de WhatsApp puede beneficiarse de Botzy.</p>
                </div>
                <i class="faq-toggle gicon-arrowr"></i>
              </div><!-- End Faq item-->

               <!-- End Faq item-->

            </div>
          </div>

        </div>
      </div>
    </section><!-- /Faq Section -->

    <!-- Call To Action 2 Section -->
    <section id="call-to-action-2" class="call-to-action-2 section dark-background d-none">

      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-10">
            <div class="text-center">
              <h3>Call To Action</h3>
              <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
              <a class="cta-btn" href="#">Call To Action</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Call To Action 2 Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section  ">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contáctanos</h2>
        <p>Estamos aquí para resolver tus dudas y ayudarte a automatizar tu negocio.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4 g-lg-5 justify-content-center">
          <!-- <div class="col-lg-5">
            <div class="info-box" data-aos="fade-up" data-aos-delay="200">
              <h3>Contact Info</h3>
              <p>Escríbenos y te responderemos lo antes posible.</p>

              <div class="info-item" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box">
                  <i class="bi bi-geo-alt"></i>
                </div>
                <div class="content">
                  <h4>Our Location</h4>
                  <p>A108 Adam Street</p>
                  <p>New York, NY 535022</p>
                </div>
              </div>

              <div class="info-item" data-aos="fade-up" data-aos-delay="400">
                <div class="icon-box">
                  <i class="bi bi-telephone"></i>
                </div>
                <div class="content">
                  <h4>Phone Number</h4>
                  <p>+1 5589 55488 55</p>
                  <p>+1 6678 254445 41</p>
                </div>
              </div>

              <div class="info-item" data-aos="fade-up" data-aos-delay="500">
                <div class="icon-box">
                  <i class="bi bi-envelope"></i>
                </div>
                <div class="content">
                  <h4>Email Address</h4>
                  <p>info@example.com</p>
                  <p>contact@example.com</p>
                </div>
              </div>
            </div>
          </div> -->

          <div class="col-lg-7 ">
            <div class="contact-form  " data-aos="fade-up" data-aos-delay="300">
             <!--  <h3>Get In Touch</h3>
              <p>Escríbenos y te responderemos lo antes posible.</p> -->

              <form  id="contactForm" method="post" class="" data-aos="fade-up" data-aos-delay="200">
                <input type="hidden" id="middle_name" calss="middle_name" name="middle_name " >
                <div class="row gy-4 mb-3">

                  <div class="col-md-6">
                    <input type="text" name="contact_name" id="contact_name" class="form-control" placeholder="Nombre" required minlength="3">
                    <div class="invalid-feedback validation_contact_name"></div>
                  </div>

                  <div class="col-md-6 ">
                    <input class="form-control" type="email" name="contact_email"  id="contact_email" placeholder="Correo" autocomplete="on"  required pattern="^[a-z0-9_\-.]+@[a-z0-9_\-.]+\.[a-z]{2,3}$"> 
                  <div class="invalid-feedback validation_contact_email"></div>
                  </div>
                  </div>

                  <div class="col-12 mb-3">
                    <input id="contact_subject" type="text" class="form-control" name="contact_subject" placeholder="Asunto">
                  </div>

                  <div class="col-12 mb-3">
                    <textarea id="contact_message" class="form-control" name="contact_message" rows="6" placeholder="Escríbenos y te responderemos lo antes posible" required=""minlength="20"></textarea>
                    <div class="invalid-feedback validation_contact_message"></div>
                  </div>

                  <div class="col-12 text-center">
                    
            <button id="submit_contact" class="btn btn-primary" type="submit">Enviar Mensaje</button>
          
          <div class="error-message p-0"></div>
          <div class="sent-message p-0"></div>
                  </div>

                </div>
              </form>

            </div>
          </div>

        </div>

      </div>

    </section><!-- /Contact Section -->
  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="gicon-arrowu"></i></a>


  