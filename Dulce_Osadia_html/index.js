// Seleccionamos el botón hamburguesa y el menú
const toggle = document.getElementById('menu-toggle');
const nav = document.getElementById('nav-link');

// Cuando se hace clic en el botón
toggle.addEventListener('click', () => {
  // alternamos la clase "show" en el menú
  nav.classList.toggle('show');
});

setTimeout(carousel, 2000); // 2000 ms = 2 segundos
