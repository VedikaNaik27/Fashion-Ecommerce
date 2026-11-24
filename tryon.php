<?php
/**
 * AURA Fashion Atelier - Virtual Try-On Studio (Interactive Demo)
 */
require_once "config/db.php";

$pageTitle = "Virtual Try-On Studio (Demo)";

// Optional pre-selected product
$preSelectedId = (int)($_GET['product_id'] ?? 0);
$catalogGarments = [];
$selectedGarment = null;

if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT product_id, name, price, category, image FROM products ORDER BY is_featured DESC, product_id ASC");
        $catalogGarments = $stmt->fetchAll();

        if ($preSelectedId > 0) {
            foreach ($catalogGarments as $cg) {
                if ((int)$cg['product_id'] === $preSelectedId) {
                    $selectedGarment = $cg;
                    break;
                }
            }
        }
        if (!$selectedGarment && !empty($catalogGarments)) {
            $selectedGarment = $catalogGarments[1] ?? $catalogGarments[0]; // Default to blazer or first item
        }
    } catch (PDOException $e) {
        $catalogGarments = [];
    }
}

require "includes/header.php";
?>

<div class="tryon-page">
  <div class="container">
    
    <div class="catalog-header">
      <span class="eyebrow">AURA CLIENT ATELIER</span>
      <h1 class="section-title">Virtual Try-On Studio</h1>
      <p class="section-subtitle">
        Visualize proportions, silhouettes, and color palettes against studio mannequins or your uploaded portrait photo.
      </p>
    </div>

    <!-- Transparent Demo Disclaimer -->
    <div class="demo-disclaimer-box" style="margin-top: 0; margin-bottom: 30px;">
      <strong>Demonstration Notice:</strong>
      This interactive studio is a <strong>client-side visual preview demo</strong> created for academic evaluation. It allows you to select or upload an image and adjust garment positioning, scale, and opacity. <em>Photorealistic 3D cloth physics and AI generative body fitting are not claimed or implemented.</em>
    </div>

    <div class="tryon-studio-grid">
      
      <!-- Left Column: Controls Deck -->
      <div class="tryon-controls-panel">
        
        <!-- Step 1: Canvas / Model Selection -->
        <div style="margin-bottom: 26px;">
          <span class="eyebrow">STEP 1 &bull; SELECT SILHOUETTE</span>
          <h4 style="font-family: var(--font-serif); font-size: 19px; margin-bottom: 12px;">Choose Canvas or Upload Photo</h4>
          
          <div class="model-selector-row">
            <div class="model-option-card active" data-model-src="assets/images/model_female.svg">
              <img src="assets/images/model_female.svg" alt="Female Model">
              <span>Female Studio</span>
            </div>
            <div class="model-option-card" data-model-src="assets/images/model_male.svg">
              <img src="assets/images/model_male.svg" alt="Male Model">
              <span>Male Studio</span>
            </div>
          </div>

          <!-- Custom User Upload Zone -->
          <div class="upload-drop-zone" id="tryonUploadTrigger">
            <div style="font-size: 24px; color: var(--color-gold-dark); margin-bottom: 4px;">📷</div>
            <strong style="font-size: 13px; display: block; color: var(--color-primary);">Upload Your Portrait / Full-Length Photo</strong>
            <small style="font-size: 11px; color: var(--color-text-muted);" id="uploadStatusText">
              Supports JPG, PNG, WebP (Processed entirely in browser)
            </small>
            <input type="file" id="tryonFileInput" accept="image/*" style="display: none;">
          </div>
        </div>

        <!-- Step 2: Garment Selection -->
        <div style="margin-bottom: 26px;">
          <span class="eyebrow">STEP 2 &bull; CHOOSE GARMENT</span>
          <h4 style="font-family: var(--font-serif); font-size: 19px; margin-bottom: 12px;">Select Garment from Collection</h4>
          
          <div class="garment-picker-scroller">
            <?php foreach ($catalogGarments as $g): 
              $isActive = ($selectedGarment && (int)$selectedGarment['product_id'] === (int)$g['product_id']);
            ?>
              <div class="garment-thumb-btn <?= $isActive ? 'active' : '' ?>"
                   data-garment-src="<?= h($g['image'] ?: 'assets/placeholder.svg') ?>"
                   data-garment-name="<?= h($g['name']) ?>"
                   data-garment-id="<?= (int)$g['product_id'] ?>"
                   title="<?= h($g['name']) ?>">
                <img src="<?= h($g['image'] ?: 'assets/placeholder.svg') ?>" 
                     alt="<?= h($g['name']) ?>"
                     onerror="this.src='assets/placeholder.svg'">
                <small><?= h($g['name']) ?></small>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Step 3: Interactive Sliders -->
        <div>
          <span class="eyebrow">STEP 3 &bull; FITTING ADJUSTMENTS</span>
          <h4 style="font-family: var(--font-serif); font-size: 19px; margin-bottom: 14px;">Refine Silhouette &amp; Scale</h4>

          <div class="slider-controls-group">
            <div class="slider-unit">
              <label>
                <span>Garment Scale</span>
                <span id="scaleValDisplay">100%</span>
              </label>
              <input type="range" id="garmentScale" min="50" max="150" value="100">
            </div>

            <div class="slider-unit">
              <label>
                <span>Vertical Alignment</span>
                <span id="yValDisplay">0px</span>
              </label>
              <input type="range" id="garmentY" min="-120" max="120" value="0">
            </div>

            <div class="slider-unit" style="grid-column: 1 / -1;">
              <label>
                <span>Fabric Opacity</span>
                <span id="opacityValDisplay">100%</span>
              </label>
              <input type="range" id="garmentOpacity" min="30" max="100" value="100">
            </div>
          </div>

          <div style="margin-top: 14px; display: flex; justify-content: flex-end;">
            <button type="button" id="resetCanvasBtn" style="font-size: 11px; text-decoration: underline; color: var(--color-text-muted);">
              Reset Adjustments
            </button>
          </div>
        </div>

      </div>

      <!-- Right Column: Interactive Studio Canvas -->
      <div class="tryon-canvas-container">
        
        <div class="canvas-viewport" id="canvasViewport">
          <!-- Background Model/Photo Layer -->
          <img src="assets/images/model_female.svg" 
               id="canvasModelLayer" 
               class="canvas-bg-layer" 
               alt="Fitting Canvas Model">
          
          <!-- Garment Overlay Layer -->
          <img src="<?= h($selectedGarment['image'] ?? 'assets/images/p2_tailored_blazer.svg') ?>" 
               id="canvasGarmentLayer" 
               class="canvas-garment-layer" 
               alt="Selected Garment Overlay">
        </div>

        <!-- Canvas Footer Details & Direct Action -->
        <div style="width: 100%; margin-top: 20px; text-align: center;">
          <div style="font-size: 14px; font-weight: 600; color: var(--color-primary); margin-bottom: 12px;">
            <span id="selectedGarmentName"><?= h($selectedGarment['name'] ?? 'Classic Tailored Blazer') ?></span>
          </div>

          <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="product_details.php?id=<?= (int)($selectedGarment['product_id'] ?? 2) ?>" 
               id="tryonProductLink" 
               class="btn btn-sm btn-primary">
              View Product Details &rarr;
            </a>

            <form method="post" action="cart_action.php" style="display: inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="product_id" value="<?= (int)($selectedGarment['product_id'] ?? 2) ?>">
              <button type="submit" class="btn btn-sm btn-outline-gold">
                + Add Garment to Bag
              </button>
            </form>
          </div>
        </div>

      </div>

    </div>

  </div>
</div>

<?php require "includes/footer.php"; ?>
