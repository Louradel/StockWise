<?php
require_once __DIR__ . '/includes/functions.php'; require_login();
$page_title = 'Dashboard';
$pdo = db();
$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalCategories = (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalSuppliers = (int)$pdo->query('SELECT COUNT(*) FROM suppliers')->fetchColumn();
$lowStock = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= reorder_level')->fetchColumn();
$recent = $pdo->query("SELECT t.*, p.name product_name FROM stock_transactions t JOIN products p ON p.id=t.product_id ORDER BY t.id DESC LIMIT 6")->fetchAll();
$products = $pdo->query('SELECT name, quantity, reorder_level FROM products ORDER BY quantity ASC LIMIT 5')->fetchAll();
require 'includes/header.php';
?>
<div class="welcome"><div><span class="eyebrow light">OVERVIEW</span><h2>Good day, <?= e($_SESSION['name']) ?>.</h2><p>Here is what is happening with your inventory today.</p></div><a class="btn white" href="products.php?action=add">＋ Add product</a></div>
<div class="stats">
  <div class="stat-card"><span class="stat-icon blue">▤</span><span class="stat-label">Total products</span><strong><?= $totalProducts ?></strong><small>Registered items</small></div>
  <div class="stat-card"><span class="stat-icon violet">▧</span><span class="stat-label">Categories</span><strong><?= $totalCategories ?></strong><small>Product groups</small></div>
  <div class="stat-card"><span class="stat-icon green">♧</span><span class="stat-label">Suppliers</span><strong><?= $totalSuppliers ?></strong><small>Registered suppliers</small></div>
  <div class="stat-card"><span class="stat-icon orange">!</span><span class="stat-label">Low stock alerts</span><strong><?= $lowStock ?></strong><small>At or below reorder level</small></div>
</div>
<div class="grid-two">
<section class="panel"><div class="panel-heading"><div><h3>Recent stock activity</h3><p>Latest stock-in and stock-out records</p></div><a href="transactions.php">View all →</a></div>
<div class="table-wrap"><table><thead><tr><th>Product</th><th>Type</th><th>Quantity</th><th>Date</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td class="strong"><?= e($r['product_name']) ?></td><td><span class="pill <?= $r['type']==='IN'?'good':'warn' ?>"><?= e($r['type']) ?></span></td><td><?= (int)$r['quantity'] ?></td><td><?= e(date('M d, Y',strtotime($r['created_at']))) ?></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="4" class="empty">No stock activity recorded yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
<section class="panel"><div class="panel-heading"><div><h3>Stock watch</h3><p>Items with the lowest quantities</p></div><a href="products.php">Products →</a></div>
<?php foreach($products as $p): $pct=$p['reorder_level']>0?min(100,($p['quantity']/$p['reorder_level'])*100):100; ?><div class="stock-row"><div><b><?= e($p['name']) ?></b><small><?= (int)$p['quantity'] ?> in stock · reorder at <?= (int)$p['reorder_level'] ?></small></div><div class="bar"><span style="width:<?= $pct ?>%"></span></div></div><?php endforeach; ?>
<?php if(!$products): ?><p class="empty">Add products to see stock levels.</p><?php endif; ?>
</section></div>
<?php require 'includes/footer.php'; ?>
