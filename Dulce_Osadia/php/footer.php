<style>
    /* Estilos para el Footer */
    .site-footer {
        background-color: #333;
        /* Un gris oscuro que contrasta bien */
        color: #fff;
        padding: 30px 0 0;
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }

    .footer-content {
        display: flex;
        justify-content: space-around;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px 30px;
        flex-wrap: wrap;
        /* Para que funcione bien en móviles */
    }

    .footer-section {
        flex: 1;
        min-width: 250px;
        /* Asegura que no se aprieten demasiado */
        margin-bottom: 20px;
    }

    .footer-section h3 a,
    .footer-section h4 {
        color: #85b6aa;
        /* Usamos tu color principal */
        margin-bottom: 15px;
        border-bottom: 2px solid #85b6aa;
        /* Un detalle visual sutil */
        padding-bottom: 5px;
        display: inline-block;
    }

    .footer-section a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-section a:hover {
        color: #85b6aa;
    }

    /* Estilos de las listas de enlaces */
    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section li {
        margin-bottom: 8px;
    }

    /* Estilos de Contacto y Redes Sociales */
    .contact span {
        display: block;
        margin-bottom: 10px;
    }

    .contact i {
        margin-right: 10px;
        color: #85b6aa;
    }

    .socials a {
        margin-right: 15px;
        font-size: 1.5em;
        display: inline-block;
        align-items: center;
    }

    /* Estilos de la parte inferior */
    .footer-bottom {
        background-color: #222;
        /* Un tono aún más oscuro */
        color: #aaa;
        text-align: center;
        padding: 15px 20px;
        font-size: 0.9em;
        border-top: 1px solid #444;
    }
</style>

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h3><a href="index.php">Dulce Osadia</a></h3>
            <p>
                Ofreciendo la mejor calidad en Chocolates. <br>
                ¡Gracias por elegirnos!
            </p>
            <div class="contact">
                <span><i class="fas fa-phone"></i> +56 9 1234 5678</span>
                <span><i class="fas fa-envelope"></i> DulceOsadia@contacto.cl</span>
            </div>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/dulce_osadia23/"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-x"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="footer-section links">
            <h4>Enlaces Rápidos</h4>
            <ul>
                <li><a href="productos.php">PRODUCTOS</a></li>
                <li><a href="nosotros.php">NUESTRA EMPRESA</a></li>
                <li><a href="compras.php">Mis compras</a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                <?php } ?>
            </ul>
        </div>

        <div class="footer-section legal">
            <h4>Información Legal</h4>
            <ul>
                <li><a href="politica_priv.php">Política de Privacidad</a></li>
                <li><a href="terminos_condiciones.php">Términos y Condiciones</a></li>
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> Dulce Osadia | Todos los derechos reservados.
    </div>
</footer>