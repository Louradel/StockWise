<?php require_once __DIR__ . '/functions.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? APP_NAME) ?> | <?= e(APP_NAME) ?></title>
  <link rel="stylesheet" href="assets/style.css">
  <script defer src="assets/app.js"></script>
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <a class="brand" href="index.php"><span class="brand-mark">S</span><span>StockWise<small>Inventory control</small></span></a>
    <div class="nav-label">WORKSPACE</div>
    <nav>
      <a class="<?= current_page()==='index.php'?'active':'' ?>" href="index.php">▦ <span>Dashboard</span></a>
      <a class="<?= current_page()==='products.php'?'active':'' ?>" href="products.php">▤ <span>Products</span></a>
      <a class="<?= current_page()==='categories.php'?'active':'' ?>" href="categories.php">▧ <span>Categories</span></a>
      <a class="<?= current_page()==='suppliers.php'?'active':'' ?>" href="suppliers.php">♧ <span>Suppliers</span></a>
      <a class="<?= current_page()==='transactions.php'?'active':'' ?>" href="transactions.php">⇄ <span>Stock transactions</span></a>
    </nav>
    <div class="sidebar-bottom">
      <div class="user-mini"><div class="avatar"><?= e(strtoupper(substr($_SESSION['name'] ?? 'A',0,1))) ?></div><div><?= e($_SESSION['name'] ?? 'Administrator') ?><small>Administrator</small></div></div>
      <a class="logout" href="logout.php">↪ Sign out</a>
    </div>
  </aside>
  <main class="main">
    <header class="topbar"><div><span class="eyebrow">INVENTORY MANAGEMENT</span><h1><?= e($page_title ?? 'Dashboard') ?></h1></div><div class="top-right"><?= date('D, d M Y') ?></div></header>
    <section class="content">
      <?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
      <?php endif; ?>
