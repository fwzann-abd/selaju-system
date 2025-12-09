<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
     * Preview Excel file before actual import so admins can review the data.
     */
    public function previewImport(Request $request)
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

            [$rows, $offset, $startingRow] = $this->extractDataRows($rows);

            $validRows = [];
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + $startingRow;

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

                $validator = Validator::make($data, [
                    'nama' => ['required', 'string', 'max:255'],
                    'nipd' => ['nullable', 'string', 'max:255'],
                    'nisn' => ['required', 'string', 'max:255'],
                    'jk' => ['required', 'in:L,P'],
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris {$rowNumber}: ".implode(', ', $validator->errors()->all());
                    continue;
                }

                $validRows[] = [
                    'row_number' => $rowNumber,
                    'nama' => $data['nama'],
                    'nipd' => $data['nipd'],
                    'nisn' => $data['nisn'],
                    'jk' => $data['jk'],
                ];
            }

            if (empty($validRows)) {
                return response()->json([
                    'message' => 'Tidak ada data valid yang dapat dipreview. Periksa kembali file Anda.',
                    'errors' => $errors,
                ], 422);
            }

            $token = (string) Str::uuid();
            Cache::put('student-import-'.$token, [
                'rows' => $validRows,
                'school_id' => $request->school_id,
                'filename' => $file->getClientOriginalName(),
            ], now()->addMinutes(30));

            return response()->json([
                'token' => $token,
                'rows' => $validRows,
                'errors' => $errors,
                'summary' => [
                    'valid' => count($validRows),
                    'invalid' => count($errors),
                ],
                'filename' => $file->getClientOriginalName(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal membaca file: '.$e->getMessage(),
            ], 422);
        }
    }

    private function extractDataRows(array $rows): array
    {
        $headerIndex = null;
        $headers = [];

        foreach ($rows as $index => $row) {
            $normalized = array_map(static function ($value) {
                return strtolower(trim((string) $value));
            }, $row);

            if (in_array('nama', $normalized, true)) {
                $headerIndex = $index;
                $headers = $normalized;
                break;
            }
        }

        if ($headerIndex === null) {
            throw new \RuntimeException('Struktur file tidak dikenali. Gunakan template import terbaru.');
        }

        $hasNumberColumn = isset($headers[0]) && in_array($headers[0], ['no', 'no.', 'nomor']);
        $offset = $hasNumberColumn ? 1 : 0;
        $startingRow = $headerIndex + 2;
        $dataRows = array_slice($rows, $headerIndex + 1);

        return [$dataRows, $offset, $startingRow];
    }

    /**
     * Import students from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_token' => ['required', 'string'],
        ]);

        $token = $request->input('import_token');
        $payload = Cache::pull('student-import-'.$token);

        if (! $payload) {
            return back()->with('error', 'Sesi import tidak ditemukan atau telah kedaluwarsa. Silakan ulangi proses preview.');
        }

        $rows = $payload['rows'] ?? [];
        $schoolId = $payload['school_id'] ?? null;

        if (! $schoolId || empty($rows)) {
            return back()->with('error', 'Data import tidak valid atau kosong.');
        }

        if (! School::where('id', $schoolId)->exists()) {
            return back()->with('error', 'Sekolah tujuan sudah tidak tersedia. Silakan ulangi proses import.');
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $row) {
            $data = [
                'school_id' => $schoolId,
                'nama' => $row['nama'] ?? '',
                'nipd' => $row['nipd'] ?? null,
                'nisn' => $row['nisn'] ?? '',
                'jk' => $row['jk'] ?? '',
            ];

            $validator = Validator::make($data, [
                'nama' => ['required', 'string', 'max:255'],
                'nipd' => ['nullable', 'string', 'max:255'],
                'nisn' => ['required', 'string', 'max:255', 'unique:students,nisn'],
                'jk' => ['required', 'in:L,P'],
            ]);

            if ($validator->fails()) {
                $skipped++;
                $rowNumber = $row['row_number'] ?? '-';
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

        return redirect()->route('admin.students.index')
            ->with($skipped > 0 ? 'warning' : 'success', $message);
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
