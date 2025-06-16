<div style="display: flex; padding: 20px;">
    <!-- Filter Sidebar -->
    <div style="width: 200px; margin-right: 20px;">
        <h3>Filter by Price</h3>
        <form method="GET" action="">
            <label>
                <input type="radio" name="price" value="low" <?= isset($_GET['price']) && $_GET['price'] === 'low' ? 'checked' : '' ?>>
                Low to High
            </label><br>
            <label>
                <input type="radio" name="price" value="high" <?= isset($_GET['price']) && $_GET['price'] === 'high' ? 'checked' : '' ?>>
                High to Low
            </label><br><br>
            <button type="submit">Apply Filter</button>
        </form>
    </div>

    <!-- Products Section -->
    <div style="flex-grow: 1;">
        <?php include 'components/product_card.php'; ?>
    </div>
</div>
