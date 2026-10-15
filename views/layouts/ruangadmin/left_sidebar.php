<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $base_url ?>/Home">
    <div class="sidebar-brand-icon">
      <img src="<?= asset() ?>/img/urbanmart.png" width="42" height="42" style="object-fit: contain; border-radius:0.5rem;" alt="">
    </div>
    <div class="sidebar-brand-text mx-3">
      <span style="display:block; font-size:1rem;">UrbanMart</span>
      <span style="display:block; font-size:0.68rem; font-weight:500; opacity:0.7;">Grocery Management</span>
    </div>
  </a>
  <?php if (isset($_SESSION['uname'])) { ?>
    <div class="sidebar-user d-none d-lg-flex align-items-center">
      <img class="rounded-circle" src="<?= $base_url ?>/img/<?= !empty($_SESSION['uphoto']) ? htmlspecialchars($_SESSION['uphoto']) : 'admin.png' ?>" width="34" height="34" style="object-fit:cover;" alt="">
      <div class="ml-2" style="min-width:0;">
        <div style="font-weight:600; font-size:0.82rem; color:#fff; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($_SESSION['uname']) ?></div>
        <div style="font-size:0.68rem; color:rgba(255,255,255,0.55);"><?= !empty($_SESSION['urole']) ? htmlspecialchars($_SESSION['urole']) : 'Member' ?></div>
      </div>
    </div>
  <?php } ?>
  <hr class="sidebar-divider my-0">
  <li class="nav-item active">
    <a class="nav-link" href="<?= $base_url ?>/Home">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>
  <hr class="sidebar-divider">
  <div class="sidebar-heading">
    Manage Store
  </div>
  <?php
    menus();
  ?>

</ul>
<!-- Sidebar -->