<?php if(!empty($_SESSION['flash_ok'])): ?>
  <div class="alert alert-success py-2"><?= $_SESSION['flash_ok']; unset($_SESSION['flash_ok']); ?></div>
<?php endif; ?>
<?php if(!empty($_SESSION['flash_err'])): ?>
  <div class="alert alert-danger py-2"><?= $_SESSION['flash_err']; unset($_SESSION['flash_err']); ?></div>
<?php endif; ?>
