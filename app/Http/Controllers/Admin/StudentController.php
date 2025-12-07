<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $search = $request->query('q', '');
        $schoolId = $request->query('school_id', '');

        $students = Student::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nipd', 'like', "%{$search}%");
            })
            ->when($schoolId, function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->with(['school:id,name', 'user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.students.index', [
            'students' => $students,
            'schools' => $schools,
            'search' => $search,
            'selectedSchool' => $schoolId,
            'pageTitle' => 'Daftar Siswa',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Siswa', 'url' => null],
            ],
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.students.create', [
            'schools' => $schools,
            'pageTitle' => 'Tambah Siswa',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Siswa', 'url' => route('admin.students.index')],
                ['label' => 'Tambah', 'url' => null],
            ],
        ]);
    }

    /**
     * Show import form for students via Excel.
     */
    public function showImportForm()
    {
        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.students.import', [
            'schools' => $schools,
            'pageTitle' => 'Tambah Siswa via Excel',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Siswa', 'url' => route('admin.students.index')],
                ['label' => 'Import', 'url' => null],
            ],
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'nama' => ['required', 'string', 'max:255'],
            'nipd' => ['nullable', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:255', 'unique:students,nisn'],
            'jk' => ['required', 'in:L,P'],
        ]);

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan');
    }

    /**
     * Show the form for editing a student.
     */
    public function edit(Student $student)
    {
        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.students.edit', [
            'student' => $student,
            'schools' => $schools,
            'pageTitle' => 'Edit Siswa',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Siswa', 'url' => route('admin.students.index')],
                ['label' => 'Edit', 'url' => null],
            ],
        ]);
    }

    /**
     * Display the specified student.
     *
     * For now redirect to edit page to provide a simple show behavior.
     */
    public function show(Student $student)
    {
        return redirect()->route('admin.students.edit', $student);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'nama' => ['required', 'string', 'max:255'],
            'nipd' => ['nullable', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:255', 'unique:students,nisn,'.$student->id],
            'jk' => ['required', 'in:L,P'],
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil diperbarui');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        if ($student->isRegistered()) {
            return back()->with('error', 'Tidak dapat menghapus siswa yang sudah terdaftar');
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus');
    }

    /**
     * Download Excel template for bulk import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Add notes section (rows 1-5)
        $sheet->setCellValue('A1', 'Catatan:');
        $sheet->setCellValue('A2', '- jk: L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A3', '- nipd: boleh kosong');
        $sheet->setCellValue('A4', '- nisn: wajib diisi dan unik');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2:A4')->getFont()->setItalic(true);

        // Add warning row (row 6)
        $sheet->setCellValue('A6', '* hapus contoh nama siswa ketika upload data');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setItalic(true);

        // Set headers (row 7) - include No column for reference
        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'nama');
        $sheet->setCellValue('C7', 'nipd');
        $sheet->setCellValue('D7', 'nisn');
        $sheet->setCellValue('E7', 'jk');
        $sheet->getStyle('A7:E7')->getFont()->setBold(true);

        // Add example data (row 8)
        $sheet->setCellValue('A8', '1');
        $sheet->setCellValue('B8', 'Contoh Nama Siswa');
        $sheet->setCellValue('C8', '12345678');
        $sheet->setCellValue('D8', '0012345678');
        $sheet->setCellValue('E8', 'L');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_siswa_'.date('Ymd_His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import students from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Detect header row and whether there's a leading "No" column
            $headers = array_map(function ($v) {
                return strtolower(trim((string) $v));
            }, $rows[0] ?? []);

            $hasNumberColumn = isset($headers[0]) && in_array($headers[0], ['no', 'no.', 'nomor']);
            $offset = $hasNumberColumn ? 1 : 0;
            $startingRow = $hasNumberColumn ? 8 : 2; // for accurate error messages

            // Remove header row
            array_shift($rows);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + $startingRow;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = [
                    'school_id' => $request->school_id,
                    'nama' => trim($row[$offset + 0] ?? ''),
                    'nipd' => ! empty($row[$offset + 1]) ? trim($row[$offset + 1]) : null,
                    'nisn' => trim($row[$offset + 2] ?? ''),
                    'jk' => strtoupper(trim($row[$offset + 3] ?? '')),
                ];

                // Validate row data
                $validator = Validator::make($data, [
                    'nama' => ['required', 'string', 'max:255'],
                    'nipd' => ['nullable', 'string', 'max:255'],
                    'nisn' => ['required', 'string', 'max:255', 'unique:students,nisn'],
                    'jk' => ['required', 'in:L,P'],
                ]);

                if ($validator->fails()) {
                    $skipped++;
                    $errors[] = "Baris {$rowNumber}: ".implode(', ', $validator->errors()->all());

                    continue;
                }

                Student::create($data);
                $imported++;
            }

            $message = "Import selesai. Berhasil: {$imported}, Dilewati: {$skipped}";
            if (! empty($errors)) {
                $message .= "\n\nError:\n".implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... dan ".(count($errors) - 10).' error lainnya.';
                }
            }

            return back()->with($skipped > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: '.$e->getMessage());
        }
    }

    /**
     * Export students to Excel.
     */
    public function export(Request $request)
    {
        $schoolId = $request->query('school_id', '');

        $students = Student::query()
            ->when($schoolId, function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->with('school:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'NIPD');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Jenis Kelamin');
        $sheet->setCellValue('E1', 'Sekolah');
        $sheet->setCellValue('F1', 'Status');

        // Style headers
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Add data
        $row = 2;
        foreach ($students as $student) {
            $sheet->setCellValue('A'.$row, $student->nama);
            $sheet->setCellValue('B'.$row, $student->nipd);
            $sheet->setCellValue('C'.$row, $student->nisn);
            $sheet->setCellValue('D'.$row, $student->jk === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E'.$row, $student->school->name ?? '-');
            $sheet->setCellValue('F'.$row, $student->isRegistered() ? 'Terdaftar' : 'Belum Terdaftar');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'siswa_'.date('Ymd_His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
