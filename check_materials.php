<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$db = $app->make('db');

echo "=== Checking Students ===\n";
$students = $db->table('students')->where('account_id', '!=', null)->take(1)->first();

if ($students) {
    echo 'Student ID: '.$students->id."\n";
    echo 'Student Name: '.$students->name."\n";

    echo "\n=== Classrooms for this Student ===\n";
    $classrooms = $db->table('classroom_students')
        ->where('student_id', $students->id)
        ->pluck('classroom_id');

    if ($classrooms->count() > 0) {
        echo 'Classroom IDs: '.implode(', ', $classrooms->toArray())."\n";

        echo "\n=== Materials in those Classrooms ===\n";
        $materials = $db->table('course_materials')
            ->whereIn('classroom_id', $classrooms)
            ->where('is_published', true)
            ->get();

        if ($materials->count() > 0) {
            foreach ($materials as $m) {
                echo '- '.$m->title.' (ID: '.$m->id.', Classroom: '.$m->classroom_id.")\n";
            }
            echo 'Total: '.$materials->count()."\n";
        } else {
            echo "No materials found\n";
        }
    } else {
        echo "Student not assigned to any classroom\n";
    }
} else {
    echo "No student found\n";
}
