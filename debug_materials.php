<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CourseMaterial;
use App\Models\Student;

$student = Student::where('account_id', '!=', null)->first();

if ($student) {
    echo 'Student: '.$student->name.' (ID: '.$student->id.")\n";
    echo 'Account ID: '.$student->account_id."\n";
    echo 'Classrooms assigned: '.$student->classrooms()->count()."\n";

    $student->classrooms()->each(function ($c) {
        echo '  - '.$c->name.' (ID: '.$c->id.")\n";
    });

    echo "\nMaterials check:\n";
    $classroomIds = $student->classrooms()->pluck('classrooms.id');
    echo 'Classroom IDs: '.$classroomIds->implode(', ')."\n";

    $materials = CourseMaterial::whereIn('classroom_id', $classroomIds)
        ->where('is_published', true)
        ->get();
    echo 'Total published materials in student classrooms: '.$materials->count()."\n";

    $materials->each(function ($m) {
        echo '  - '.$m->title.' (Classroom ID: '.$m->classroom_id.', Published: '.($m->is_published ? 'yes' : 'no').")\n";
    });
} else {
    echo "No student found\n";
}
