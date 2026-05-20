$student = App\Models\Student::where('account_id', '!=', null)->first();
if ($student) {
    echo "Student: " . $student->name . " (ID: " . $student->id . ")\n";
    echo "Classrooms: " . $student->classrooms()->count() . "\n";
    $classroomIds = $student->classrooms()->pluck('classrooms.id');
    $materials = App\Models\CourseMaterial::whereIn('classroom_id', $classroomIds)->where('is_published', true)->count();
    echo "Materials: " . $materials . "\n";
}
