<?php
use App\Http\Controllers\FieldController;
use Illuminate\Http\Request;

// Initialize Laravel
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "<h1>Field Debug Page</h1>";
echo "<h2>Testing FieldController::index()</h2>";

$controller = new FieldController();
$response = $controller->index();

if ($response instanceof Illuminate\View\View) {
    $data = $response->getData();
    $topFields = $data["topFields"];
    
    echo "<p><strong>Data being passed to view:</strong></p>";
    echo "<ul>";
    foreach ($topFields as $field) {
        echo "<li>ID: {$field->id} | Name: {$field->name} | Type: {$field->type} | Price: Rp " . number_format($field->price_per_hour, 0, ",", ".") . "</li>";
    }
    echo "</ul>";
    
    echo "<h2>Testing FieldController::allFields()</h2>";
    
    $request = new Request();
    $allFieldsResponse = $controller->allFields($request);
    
    if ($allFieldsResponse instanceof Illuminate\View\View) {
        $allData = $allFieldsResponse->getData();
        $fieldsGrouped = $allData["fieldsGrouped"];
        
        echo "<p><strong>All Fields data:</strong></p>";
        echo "<ul>";
        foreach ($fieldsGrouped as $venue) {
            echo "<li>Name: {$venue->name} | Type: {$venue->display_type} | Courts: {$venue->total_courts}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>Error: Unexpected response type</p>";
}

echo "<h2>Direct Database Query</h2>";
echo "<p><strong>All fields in database:</strong></p>";
echo "<ul>";
$allFields = App\Models\Field::all();
foreach ($allFields as $field) {
    echo "<li>ID: {$field->id} | {$field->name} | {$field->type} | Court {$field->court_number}</li>";
}
echo "</ul>";
