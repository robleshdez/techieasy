 
!(function($) {
  "use strict";


// Register
$('#submit_contact').on('click', function(event) {
  var form = $('#contactForm');
  if (form[0].checkValidity()) {
    contactEmail();
  } else {
    event.preventDefault();
    // Mostrar mensajes de validación
    var datas = {
      "contact_email": {
        "valueMissing": "Por favor, ingresa tu correo electrónico.",
        "patternMismatch": "Ingresa un correo electrónico válido."
      },
      "contact_name": {
        "valueMissing": "Por favor, no olvides tu nombre.",
        "tooShort": "El nombre debe ser mayor a 3 caracteres.",
      },
      "contact_message": {
        "valueMissing": "Por favor, escribe tu mensaje.",
        "tooShort": "El mensaje debe tener al menos a 20 caracteres.",
      }
    };
    validationFeedback(form, datas)
  }
  form.addClass('was-validated');
});

function contactEmail(){
  var alertOptions = {icon: 'error', title:''};
  var swalOptions={}
  var formData = $('#contactForm').serialize();
   var email = $('#contact_email').val();
  /* Mostramos un loader hasta que se procese la solocitud */
  showSpinner('#submit_contact', 'Enviando...')
  $.ajax({
    type: 'POST',   
    url: site_url+'app/controllers/AjaxController.php',
    data: formData + '&action=contactEmail&controller=home/HomeController',   
dataType: 'json',
success: function(response) {
  showSpinner('#submit_contact', 'Enviar Mensaje', false)
  if(response.status == 'success') {
    
     swalOptions = {
      icon: 'success',
      title: '¡Gracias por contactarnos!',
      html: '<p>Te responderemos en breve.</p>',
    };

    $('#contactForm')[0].reset();;// Al login
    swalAlert(swalOptions)
  }
  else { 
      swalOptions = {
      icon: 'error',
      title: '¡Opss!',
      html: '<p>Parece que hubo un error al enviar tu mensaje.</p><p>Inténtelo más tarde.</p>',
    };
    swalAlert(swalOptions) 
        
      }
    },
    error: function(xhr, status, error) { 
      showSpinner('#submit_contact', 'Enviar Mensaje', false)
      alertOptions.title=ajaxError(status, error).title 
      alertToast(alertOptions);
    }
  });
  
}
  











  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
 const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

function mobileNavToogle() {
  const body = document.querySelector('body');
  body.classList.toggle('mobile-nav-active');
  mobileNavToggleBtn.classList.toggle('gicon-menu');
  mobileNavToggleBtn.classList.toggle('gicon-close');

  // Evitar scroll cuando el menú está activo
  if (body.classList.contains('mobile-nav-active')) {
    body.style.overflow = 'hidden'; // Deshabilita el scroll
  } else {
    body.style.overflow = ''; // Restaura el scroll
  }
}

if (mobileNavToggleBtn) {
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
}


  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

   
  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Frequently Asked Questions Toggle
   */
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
  faqItem.addEventListener('click', () => {
    const parent = faqItem.parentNode; // Obtener el elemento padre de h3 o faq-toggle
    const siblings = parent.parentNode.querySelectorAll('.faq-item'); // Obtener todos los elementos faq-item

    // Eliminar la clase faq-active de todos los hermanos
    siblings.forEach((sibling) => sibling.classList.remove('faq-active'));

    // Agregar la clase faq-active al elemento actual
    parent.classList.add('faq-active');
  });
});


  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      // Añadimos un evento de clic para evitar el cambio en la URL
    navmenulink.addEventListener('click', function(event) {
      event.preventDefault(); // Evitar el cambio de URL
      section.scrollIntoView({ behavior: 'smooth' }); // Desplazar a la sección con un desplazamiento suave
    });

      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

})(jQuery);
