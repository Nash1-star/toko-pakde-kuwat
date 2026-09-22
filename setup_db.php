<?php

$dir = __DIR__ . '/database/migrations/';

function updateMigration($file, $schema) {
    global $dir;
    $content = file_get_contents($dir . $file);
    $content = preg_replace('/Schema::create\(.*?, function \(Blueprint \$table\) \{(.*?)\}\);/s', "Schema::create('" . explode('_', $file, 5)[4] . "', function (Blueprint \$table) {\n$schema\n        });", $content);
    // Fix the table name since it uses explode logic which is flawed for the ones without dates, but we'll manually replace
}

// Just replace contents directly
file_put_contents($dir . '0001_01_01_000000_create_users_table.php', str_replace(
    '$table->string(\'name\');',
    '$table->string(\'name\');
            $table->string(\'pin\')->nullable();
            $table->string(\'role\')->default(\'operator\');',
    file_get_contents($dir . '0001_01_01_000000_create_users_table.php')
));

file_put_contents($dir . '2026_09_22_080544_create_categories_table.php', str_replace(
    '$table->id();',
    '$table->id();
            $table->string(\'name\');
            $table->text(\'description\')->nullable();',
    file_get_contents($dir . '2026_09_22_080544_create_categories_table.php')
));

file_put_contents($dir . '2026_09_22_080545_create_products_table.php', str_replace(
    '$table->id();',
    '$table->id();
            $table->foreignId(\'category_id\')->constrained()->onDelete(\'cascade\');
            $table->string(\'barcode\')->nullable()->unique();
            $table->string(\'name\');
            $table->decimal(\'purchase_price\', 15, 2)->default(0);
            $table->decimal(\'selling_price\', 15, 2)->default(0);
            $table->integer(\'min_stock\')->default(0);
            $table->integer(\'current_stock\')->default(0);',
    file_get_contents($dir . '2026_09_22_080545_create_products_table.php')
));

file_put_contents($dir . '2026_09_22_080546_create_shifts_table.php', str_replace(
    '$table->id();',
    '$table->id();
            $table->foreignId(\'user_id\')->constrained();
            $table->dateTime(\'start_time\')->useCurrent();
            $table->dateTime(\'end_time\')->nullable();
            $table->decimal(\'starting_cash\', 15, 2)->default(0);
            $table->decimal(\'expected_cash\', 15, 2)->default(0);
            $table->decimal(\'actual_cash\', 15, 2)->nullable();
            $table->decimal(\'difference\', 15, 2)->nullable();
            $table->text(\'notes\')->nullable();
            $table->enum(\'status\', [\'open\', \'closed\'])->default(\'open\');',
    file_get_contents($dir . '2026_09_22_080546_create_shifts_table.php')
));

file_put_contents($dir . '2026_09_22_080546_create_transactions_table.php', str_replace(
    '$table->id();',
    '$table->id();
            $table->foreignId(\'user_id\')->constrained();
            $table->foreignId(\'shift_id\')->constrained();
            $table->decimal(\'total_amount\', 15, 2)->default(0);
            $table->string(\'payment_method\')->default(\'Tunai\');',
    file_get_contents($dir . '2026_09_22_080546_create_transactions_table.php')
));

file_put_contents($dir . '2026_09_22_080547_create_transaction_details_table.php', str_replace(
    '$table->id();',
    '$table->id();
            $table->foreignId(\'transaction_id\')->constrained()->onDelete(\'cascade\');
            $table->foreignId(\'product_id\')->constrained();
            $table->integer(\'qty\')->default(1);
            $table->decimal(\'price\', 15, 2)->default(0);
            $table->decimal(\'subtotal\', 15, 2)->default(0);',
    file_get_contents($dir . '2026_09_22_080547_create_transaction_details_table.php')
));

file_put_contents($dir . '2026_09_22_080548_create_stock_mutations_table.php', str_replace(
    '$table->id();',
    '$table->id();
            $table->foreignId(\'product_id\')->constrained()->onDelete(\'cascade\');
            $table->foreignId(\'user_id\')->constrained();
            $table->enum(\'type\', [\'sale\', \'in_supplier\', \'out_damage\', \'return\']);
            $table->integer(\'qty\');
            $table->integer(\'balance\');
            $table->string(\'description\')->nullable();',
    file_get_contents($dir . '2026_09_22_080548_create_stock_mutations_table.php')
));

// Update Models
$models = ['User', 'Category', 'Product', 'Shift', 'Transaction', 'TransactionDetail', 'StockMutation'];
foreach($models as $model) {
    $f = __DIR__ . "/app/Models/$model.php";
    if(file_exists($f)) {
        $c = file_get_contents($f);
        if(!str_contains($c, '$guarded = []')) {
            $c = str_replace('use HasFactory;', "use HasFactory;\n    protected \$guarded = [];\n", $c);
            file_put_contents($f, $c);
        }
    }
}
echo "Done";
