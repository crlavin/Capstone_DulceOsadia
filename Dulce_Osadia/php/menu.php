<style>
    /* Estilos básicos para el botón y el menú */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown a {
        background-color: #85b6aa;
        color: white;
        padding: 10px;
        cursor: pointer;
    }

    .dropdown a li {
        list-style: none;
    }

    .dropdown a li i {
        margin-right: 5px;
    }

    .dropdown-menu {
        color: #85b6aa;
        display: none;
        position: absolute;
        min-width: 160px;
        z-index: 1;

    }

    .dropdown-menu a {
        color: white;
        padding: 8px;
        text-decoration: none;
        display: block;
        border: 1px solid white;
    }

    .dropdown-menu a:hover {
        background-color: #85b6aa;
    }

    .show {
        display: block;
    }
</style>

<nav class="fadeIn">
    <div class="img_brand">
        <a href="index.php"><img src="../img/Perfil_instagram.png" alt="" width="50px"></a>
    </div>
    <div class="nav_options">
        <ul>
            <li><a href="index.php">INICIO</a></li>
            <li><a href="productos.php">PRODUCTOS</a></li>
            <li><a href="nosotros.php">NUESTRA EMPRESA</a></li>
            <li><a href="politica_priv.php">POLITICA DE PRIVACIDAD</a></li>
            <li><a href="terminos_condiciones.php">TERMINOS Y CONDICIONES</a></li>
            <li><a href="../php/checkout.php"><i class="fas fa-shopping-cart"></i> CARRITO <span id="num_cart"><?php echo $num_cart; ?></span></a></li>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <div class="dropdown">
                    <a id="btn-session" onclick="toggleDropdown()">
                        <i class="fas fa-user"></i> <?php echo strtoupper($_SESSION['user_name']); ?>
                    </a>
                    <div class="dropdown-menu" id="dropdown-menu">
                        <a class="dropdown-item" href="logout.php">Cerrar sesión</a>
                        <a class="dropdown-item" href="compras.php">Mis compras</a>
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') { ?>
                            <a class="dropdown-item" href="inicioadmin.php">Panel de Gestión</a>
                            <a class="dropdown-item" href="historial.php">Historial de insumos</a>
                            <a class="dropdown-item" href="procesar.php">Crear Receta</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <li><a href="login.php"><i class="fas fa-user"></i> LOGIN</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>

<script>
    function toggleDropdown() {
        document.getElementById("dropdown-menu").classList.toggle("show");
    }

    // CORRECCIÓN: Lógica mejorada para cerrar el menú
    window.addEventListener('click', function(event) {
        // Si el elemento en el que se hizo clic NO está dentro de un elemento con la clase .dropdown
        if (!event.target.closest('.dropdown')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    });
</script>