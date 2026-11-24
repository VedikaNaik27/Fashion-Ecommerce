</main>

<!-- High-Fashion Footer -->
<footer class="site-footer">
  <div class="footer-top">
    <div class="container footer-grid">
      
      <!-- Column 1: Brand Story -->
      <div class="footer-col brand-col">
        <a class="footer-logo" href="index.php">AURA<span class="gold-dot">.</span></a>
        <p class="brand-tagline">ATELIER DE MODE</p>
        <p class="brand-desc">
          Thoughtfully curated minimalist garments, refined silhouettes, and sustainable organic fabrics crafted for conscious modern life.
        </p>
        <div class="brand-badges">
          <span class="pill-badge">Ethical Craft</span>
          <span class="pill-badge">Zero Compromise</span>
          <span class="pill-badge">Natural Fibers</span>
        </div>
      </div>

      <!-- Column 2: Collections -->
      <div class="footer-col">
        <h4 class="footer-title">Collections</h4>
        <ul class="footer-links">
          <li><a href="products.php?category=Women">Women's Ready-to-Wear</a></li>
          <li><a href="products.php?category=Men">Men's Tailoring &amp; Casual</a></li>
          <li><a href="products.php?category=Kids">Kids Organic Capsule</a></li>
          <li><a href="products.php?category=Accessories">Leather Goods &amp; Jewels</a></li>
          <li><a href="products.php?sort=newest">New Arrivals</a></li>
        </ul>
      </div>

      <!-- Column 3: Client Experience & Demos -->
      <div class="footer-col">
        <h4 class="footer-title">Studio &amp; Services</h4>
        <ul class="footer-links">
          <li><a href="stylist.php">AI Stylist Studio <span class="badge-subtle">Demo</span></a></li>
          <li><a href="tryon.php">Virtual Fitting Room <span class="badge-subtle">Demo</span></a></li>
          <li><a href="orders.php">Track Order History</a></li>
          <li><a href="wishlist.php">Saved Wishlist</a></li>
          <li><a href="cart.php">Shopping Bag</a></li>
        </ul>
      </div>

      <!-- Column 4: Newsletter Demo & Concierge -->
      <div class="footer-col newsletter-col">
        <h4 class="footer-title">The AURA Journal</h4>
        <p class="newsletter-sub">Subscribe to receive private seasonal releases, styling lookbooks, and VIP access.</p>
        <form class="footer-newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing to the AURA Journal preview!'); this.reset();">
          <input type="email" placeholder="Enter your email address" required aria-label="Email for newsletter">
          <button type="submit">Join</button>
        </form>
        <div class="demo-notice-mini">
          <small>College Demonstration Project · Built with PHP, MySQL &amp; Vanilla JS</small>
        </div>
      </div>

    </div>
  </div>

  <!-- Bottom Legal & Disclaimer Bar -->
  <div class="footer-bottom">
    <div class="container bottom-inner">
      <p class="copyright">
        &copy; <?= date("Y"); ?> AURA Fashion Atelier. All rights reserved.
      </p>
      <div class="academic-disclaimer">
        <span>Academic Mini-Project Demonstration &bull; Powered by XAMPP (Apache + MySQL + PHP)</span>
      </div>
      <div class="footer-bottom-links">
        <span>Privacy Policy</span>
        <span>Terms of Service</span>
        <span>Demonstration Notes</span>
      </div>
    </div>
  </div>
</footer>

<script src="assets/app.js"></script>
</body>
</html>
