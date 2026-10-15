  <!-- TopBar -->
  <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
          <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <ul class="navbar-nav ml-auto">
            <?php
            global $tx;
            $topLowStock = (int)GroceryProduct::scalar("SELECT COUNT(*) FROM {$tx}grocery_products WHERE status = 'active' AND stock_quantity <= reorder_level");
            $topDue = (float)GrocerySale::scalar("SELECT IFNULL(SUM(due_amount),0) FROM {$tx}grocery_sales WHERE due_amount > 0 AND status = 'completed'");
            ?>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="navbar-search" action="<?= $base_url ?>/GroceryProduct" method="get">
                  <div class="input-group">
                    <input type="text" name="q" class="form-control bg-light border-1 small" placeholder="Search products…"
                      aria-label="Search" aria-describedby="basic-addon2" style="border-color: #3f51b5;">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter"><?= $topLowStock ?></span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/GroceryStock">
                  <div class="mr-3">
                    <div class="icon-circle bg-warning">
                      <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">Low stock</div>
                    <span class="font-weight-bold"><?= $topLowStock ?> product(s) at or below reorder level.</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/GrocerySale">
                  <div class="mr-3">
                    <div class="icon-circle bg-danger">
                      <i class="fas fa-hand-holding-usd text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">Receivables</div>
                    <span class="font-weight-bold"><?= number_format($topDue, 2) ?> outstanding on completed invoices.</span>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="<?= $base_url ?>/GroceryReport">View Reports</a>
              </div>
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <?php if (!empty($_SESSION['uphoto'])) { ?>
                  <img class="img-profile rounded-circle" src="<?= $base_url ?>/img/<?= htmlspecialchars($_SESSION['uphoto']) ?>" style="max-width: 60px">
                <?php } else { ?>
                  <img class="img-profile rounded-circle" src="<?= $base_url ?>/img/admin.png" style="max-width: 60px">
                <?php } ?>
                <span class="ml-2 d-none d-lg-inline small text-dark"><?= isset($_SESSION['uname']) ? htmlspecialchars($_SESSION['uname']) : 'User' ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="<?= $base_url ?>/GrocerySettings">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= $base_url ?>/logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- Topbar -->