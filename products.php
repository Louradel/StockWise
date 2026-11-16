<?php
require_once __DIR__ . '/includes/functions.php'; require_login(); $pdo=db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $action=$_POST['action']??'';
    try {
        if ($action==='save') {
            $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $sku=trim($_POST['sku']??'');
            $category=(int)($_POST['category_id']??0) ?: null; $supplier=(int)($_POST['supplier_id']??0) ?: null;
            $price=(float)($_POST['price']??0); $qty=(int)($_POST['quantity']??0); $reorder=(int)($_POST['reorder_level']??5);
            if ($name==='' || $sku==='') throw new RuntimeException('Product name and SKU are required.');
            if ($id) { $s=$pdo->prepare('UPDATE products SET name=?,sku=?,category_id=?,supplier_id=?,price=?,quantity=?,reorder_level=? WHERE id=?'); $s->execute([$name,$sku,$category,$supplier,$price,$qty,$reorder,$id]); }
            else { $s=$pdo->prepare('INSERT INTO products(name,sku,category_id,supplier_id,price,quantity,reorder_level) VALUES(?,?,?,?,?,?,?)'); $s->execute([$name,$sku,$category,$supplier,$price,$qty,$reorder]); }
            flash('Product saved successfully.');
        } elseif ($action==='delete') {
            $s=$pdo->prepare('DELETE FROM products WHERE id=?'); $s->execute([(int)$_POST['id']]); flash('Product deleted.');
        }
    } catch(Throwable $ex) { flash($ex instanceof PDOException?'Could not save. Check that the SKU is unique and the product has no stock history.':$ex->getMessage(),'error'); }
    redirect('products.php');
}
$edit=null;
if (($_GET['action']??'')==='edit') { $s=$pdo->prepare('SELECT * FROM products WHERE id=?'); $s->execute([(int)($_GET['id']??0)]); $edit=$s->fetch(); }
$categories=$pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$suppliers=$pdo->query('SELECT id,name FROM suppliers ORDER BY name')->fetchAll();
$search=trim($_GET['q']??''); $stmt=$pdo->prepare("SELECT p.*,c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.name LIKE ? OR p.sku LIKE ? ORDER BY p.id DESC"); $stmt->execute(["%$search%","%$search%"]); $rows=$stmt->fetchAll();
$page_title='Products'; require 'includes/header.php';
?>
<div class="page-actions"><form class="search" method="get"><input name="q" value="<?= e($search) ?>" placeholder="Search product or SKU"><button class="btn secondary">Search</button></form><a class="btn primary" href="products.php?action=add">＋ Add product</a></div>
<?php if(($_GET['action']??'')==='add' || $edit): ?>
<section class="panel form-panel"><div class="panel-heading"><div><h3><?= $edit?'Edit product':'Add new product' ?></h3><p>Enter product and stock information.</p></div><a href="products.php">Close ✕</a></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<label>Product name<input name="name" required value="<?= e($edit['name']??'') ?>"></label><label>SKU / Code<input name="sku" required value="<?= e($edit['sku']??'') ?>"></label>
<label>Category<select name="category_id"><option value="">Unassigned</option><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (($edit['category_id']??'')==$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label>Supplier<select name="supplier_id"><option value="">Unassigned</option><?php foreach($suppliers as $s): ?><option value="<?= $s['id'] ?>" <?= (($edit['supplier_id']??'')==$s['id'])?'selected':'' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></label>
<label>Unit price<input type="number" step="0.01" min="0" name="price" value="<?= e($edit['price']??'0.00') ?>"></label><label>Quantity<input type="number" min="0" name="quantity" value="<?= e($edit['quantity']??0) ?>"></label><label>Reorder level<input type="number" min="0" name="reorder_level" value="<?= e($edit['reorder_level']??5) ?>"></label>
<div class="form-buttons"><button class="btn primary">Save product</button><a class="btn secondary" href="products.php">Cancel</a></div></form></section>
<?php endif; ?>
<section class="panel"><div class="panel-heading"><div><h3>Product inventory</h3><p><?= count($rows) ?> product record(s)</p></div></div><div class="table-wrap"><table><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Quantity</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td class="strong"><?= e($r['name']) ?></td><td><?= e($r['sku']) ?></td><td><?= e($r['category_name']??'—') ?></td><td>₱<?= number_format((float)$r['price'],2) ?></td><td><?= (int)$r['quantity'] ?></td><td><span class="pill <?= $r['quantity']<=$r['reorder_level']?'warn':'good' ?>"><?= $r['quantity']<=$r['reorder_level']?'Low stock':'In stock' ?></span></td><td class="actions"><a href="products.php?action=edit&id=<?= $r['id'] ?>">Edit</a><form method="post" data-confirm="Delete this product?"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="link danger">Delete</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="7" class="empty">No products found.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require 'includes/footer.php'; ?>
