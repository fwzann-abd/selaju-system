<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\Request;

// Create a teacher
$teacher = Teacher::factory()->create();

// Create a classroom
$classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
echo "Created classroom: {$classroom->name} with slug: {$classroom->slug}\n";

// Update the classroom
$controller = app(\App\Http\Controllers\Api\ClassroomController::class);
$request = new Request(['name' => 'Updated Classroom Name']);
$response = $controller->update($request, $classroom->id);

$data = json_decode($response->getContent());
echo "Updated classroom: {$data->data->name} with slug: {$data->data->slug}\n";

echo "Test completed successfully!\n";
