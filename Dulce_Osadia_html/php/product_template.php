<?php
// Plantilla reutilizable de detalle de producto
// Espera que exista un array $product con las claves:
// title, subtitle (opcional), price (entero), sku, category, image, available (bool)
?>
<main class="product-detail">
  <div class="product-gallery">
    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" />
  </div>

  <div class="product-info">
    <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
    <?php if (!empty($product['subtitle'])): ?>
      <p class="product-subtitle"><?php echo htmlspecialchars($product['subtitle']); ?></p>
    <?php endif; ?>

    <p class="product-price">$<?php echo number_format((int)$product['price'], 0, ',', '.'); ?></p>

    <p class="product-meta"><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
    <p class="product-meta"><strong>Categoría:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
    <p class="product-stock <?php echo !empty($product['available']) ? 'in-stock' : 'out-stock'; ?>">
      <?php echo !empty($product['available']) ? 'Hay existencias' : 'Sin stock'; ?>
    </p>

    <div class="qty-row">
      <label for="qty">Cantidad</label>
      <div class="qty-control">
        <button type="button" class="qty-btn" data-change="-1">-</button>
        <input type="number" id="qty" value="1" min="1" />
        <button type="button" class="qty-btn" data-change="1">+</button>
      </div>
    </div>

    <button class="btn-addcart">Añadir al carrito</button>

    <div class="share">
      <span>Compartir:</span>
      <a href="#" class="share-btn fb" aria-label="Compartir en Facebook">f</a>
      <a href="#" class="share-btn tw" aria-label="Compartir en Twitter">x</a>
      <a href="#" class="share-btn pin" aria-label="Compartir en Pinterest">p</a>
      <a href="#" class="share-btn in" aria-label="Compartir en LinkedIn">in</a>
      <a href="#" class="share-btn wa" aria-label="Compartir en WhatsApp">wa</a>
    </div>
  </div>
</main>

<script>
  // Control de cantidad
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById('qty');
      const change = parseInt(btn.dataset.change, 10);
      const next = Math.max(1, (parseInt(input.value, 10) || 1) + change);
      input.value = next;
    });
  });
</script>

<?php include_once __DIR__ . '/footer.php'; ?>