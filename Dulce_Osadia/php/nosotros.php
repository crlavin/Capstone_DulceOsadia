<?php
require 'config/database.php';
require '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Dulce Osadia</title>
    <meta name="description" content="Tienda en línea de dulce osadia.">
    <meta name="keywords" content="dulce osadia, tienda de chocolates">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <!-- stylesheet se refiere a la hoja de estilos, esto hace que agarre la info de esta misma -->
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css">
    <link rel="stylesheet" type="text/html" href="../php/productos.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include 'menu.php'; ?>
    <style>
        body {
            background-color: #fed794;
            background-image: url("../img/Patrones_rosados/Recurso_108.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }

        /* Resaltar texto y mejorar legibilidad de los párrafos */
        .tpolpriv {
            text-align: center;
            font-size: 2rem;
            color: #3a2a16;
            /* Marrón oscuro para buen contraste */
            margin: 24px auto 12px;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .seccion_polpriv {
            max-width: 980px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.92);
            padding: 28px 32px;
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
            backdrop-filter: saturate(1.1);
        }

        .seccion_polpriv p {
            font-size: 1.14rem;
            /* ~18px */
            line-height: 1.75;
            color: #2e2415;
            margin: 0 0 16px;
            letter-spacing: 0.2px;
        }

        .seccion_polpriv h2 {
            font-size: 1.4rem;
            color: #4a331c;
            margin: 18px 0 10px;
            border-bottom: 2px solid #e5c07b;
            display: inline-block;
            padding-bottom: 4px;
        }
    </style>

    <!--Aqui comienza sobre nosotros-->


    <div class="seccion_polpriv fadeInUp">
        <h1 class="tpolpriv flash">¡Bienvenidos a "Dulce Osadía"!</h1>
        <p>En "Dulce Osadía", creemos que el chocolate no es solo un placer, sino una experiencia que despierta los sentidos y alegra el corazón.
            Nos dedicamos a ofrecerte los más exquisitos chocolates artesanales, elaborados con ingredientes de la más alta calidad y con un toque de pasión en cada detalle.</p>

        <p>En un mundo donde el sabor y la autenticidad marcan la diferencia, en "Dulce Osadía" nos enorgullece crear productos que combinan tradición, creatividad y excelencia.
            Cada pieza de chocolate es una muestra de nuestro compromiso por ofrecerte momentos dulces e inolvidables.</p>

        <p>Nuestros chocolates son elaborados cuidadosamente por manos expertas, utilizando cacao seleccionado y recetas que equilibran a la perfección lo clásico y lo innovador.
            Ofrecemos una amplia gama de productos: bombones rellenos, tabletas artesanales, trufas, y chocolates personalizados para todo tipo de ocasión.</p>

        <p>Nuestro equipo está comprometido con brindarte una atención cálida y un servicio eficiente, asegurando que cada pedido llegue a tus manos con la frescura y el cuidado que mereces.</p>

        <p>En "Dulce Osadía", sabemos que un buen chocolate puede transformar un día común en algo especial. ¡Únete a nosotros y descubre el placer de compartir momentos únicos, llenos de dulzura y osadía!</p>

        <h2>Sobre Nosotros</h2>
        <p>"Dulce Osadía" es una pyme chilena dedicada a la elaboración y venta de chocolates artesanales.
            Fundada en 2025, nuestra empresa nació del amor por el cacao y del deseo de ofrecer productos auténticos, deliciosos y elaborados con dedicación.</p>

        <p>Desde nuestros comienzos, nos hemos destacado por utilizar ingredientes naturales y técnicas tradicionales, adaptadas a las tendencias actuales del mercado.
            Nuestra misión es endulzar la vida de nuestros clientes con productos que representen calidad, pasión y creatividad.</p>

        <p>Con un equipo apasionado por el arte chocolatero, en "Dulce Osadía" nos esforzamos por ofrecer una experiencia única, desde la compra hasta el primer bocado.
            Cada cliente es parte de nuestra historia, y cada chocolate refleja nuestro compromiso con la excelencia.</p>

        <h2>Oportunidad del Proyecto</h2>
        <p>El proyecto "Dulce Osadía" surge en un momento ideal para el mercado de la repostería artesanal y los productos gourmet.
            En los últimos años, ha crecido el interés por los alimentos artesanales y los productos locales, especialmente aquellos que combinan sabor, presentación y calidad.</p>



    </div>
    <!--Aqui termina los terminos y condiciones-->
</body>

<?php include 'footer.php'; ?>

</html>