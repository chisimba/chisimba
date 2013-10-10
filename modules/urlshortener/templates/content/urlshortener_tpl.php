<?php if (isset($this->short)) : ?>
 <p><?php echo htmlspecialchars($this->url); ?> -> <?php echo htmlspecialchars($this->short); ?></p>
<?php endif; ?>
<form method="post">
 <p>
  <label for="url">Long URL:</label>
  <input id="url" name="url" type="text">
  <button>Shorten</button>
 </p>
</form>
