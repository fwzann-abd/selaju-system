<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get student accounts (accounts that have a student record)
        $studentAccounts = Account::whereHas('student')->limit(5)->get();
        $teacherAccounts = Account::whereHas('teacher')->limit(3)->get();

        if ($studentAccounts->isEmpty() && $teacherAccounts->isEmpty()) {
            return;
        }

        $now = Carbon::now();

        // ── Notifications for Students ──────────────────────────────────
        foreach ($studentAccounts as $acc) {
            $items = [
                [
                    'type' => Notification::TYPE_ASSIGNMENT_NEW,
                    'title' => 'Tugas Baru: Membuat Website Portfolio',
                    'body' => 'Pak Budi telah memberikan tugas baru di kelas RPL. Deadline: 3 hari lagi.',
                    'link' => '/assignments',
                    'ago' => 10,
                ],
                [
                    'type' => Notification::TYPE_MATERIAL_UPLOADED,
                    'title' => 'Materi Baru: Modul HTML & CSS',
                    'body' => 'Materi baru telah diunggah di kelas Pemrograman Web.',
                    'link' => '/materials',
                    'ago' => 45,
                ],
                [
                    'type' => Notification::TYPE_ATTENDANCE_RECORDED,
                    'title' => 'Absensi Tercatat: Hadir',
                    'body' => 'Kehadiran Anda di mata pelajaran Basis Data telah dicatat oleh Pak Ahmad.',
                    'link' => '/attendance',
                    'ago' => 120,
                ],
                [
                    'type' => Notification::TYPE_ANNOUNCEMENT,
                    'title' => 'Pengumuman: UTS Minggu Depan',
                    'body' => 'UTS semester genap akan dilaksanakan mulai tanggal 12 Mei 2026.',
                    'link' => '/announcements',
                    'ago' => 180,
                ],
                [
                    'type' => Notification::TYPE_ASSIGNMENT_GRADED,
                    'title' => 'Nilai Tugas: Database Design — 85/100',
                    'body' => 'Tugas ERD Perpustakaan telah dinilai oleh Pak Ahmad.',
                    'link' => '/assignments',
                    'ago' => 300,
                ],
                [
                    'type' => Notification::TYPE_MATERIAL_UPLOADED,
                    'title' => 'Materi Baru: Tutorial Git & GitHub',
                    'body' => 'Video tutorial Git telah diunggah oleh Pak Budi.',
                    'link' => '/materials',
                    'ago' => 600,
                ],
                [
                    'type' => Notification::TYPE_ATTENDANCE_RECORDED,
                    'title' => 'Absensi Tercatat: Terlambat',
                    'body' => 'Anda tercatat terlambat di mata pelajaran Jaringan Komputer.',
                    'link' => '/attendance',
                    'ago' => 1440,
                    'read' => true,
                ],
                [
                    'type' => Notification::TYPE_ASSIGNMENT_NEW,
                    'title' => 'Tugas Baru: Laporan Praktikum Jarkom',
                    'body' => 'Bu Siti memberikan tugas laporan praktikum. Kumpulkan sebelum Jumat.',
                    'link' => '/assignments',
                    'ago' => 2880,
                    'read' => true,
                ],
                [
                    'type' => Notification::TYPE_ANNOUNCEMENT,
                    'title' => 'Pengumuman: Jadwal Remedial',
                    'body' => 'Jadwal remedial mata pelajaran Matematika telah dirilis.',
                    'link' => '/announcements',
                    'ago' => 4320,
                    'read' => true,
                ],
                [
                    'type' => Notification::TYPE_MATERIAL_UPLOADED,
                    'title' => 'Materi Baru: Algoritma Sorting',
                    'body' => 'Video penjelasan bubble sort, selection sort, dan quick sort.',
                    'link' => '/materials',
                    'ago' => 7200,
                    'read' => true,
                ],
            ];

            foreach ($items as $item) {
                Notification::create([
                    'account_id' => $acc->uuid,
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'body' => $item['body'],
                    'link' => $item['link'],
                    'data' => ['icon' => Notification::$icons[$item['type']] ?? '🔔'],
                    'read_at' => ! empty($item['read']) ? $now->copy()->subMinutes($item['ago']) : null,
                    'created_at' => $now->copy()->subMinutes($item['ago']),
                    'updated_at' => $now->copy()->subMinutes($item['ago']),
                ]);
            }
        }

        // ── Notifications for Teachers ──────────────────────────────────
        foreach ($teacherAccounts as $acc) {
            $items = [
                [
                    'type' => Notification::TYPE_SUBMISSION_RECEIVED,
                    'title' => 'Tugas Dikumpulkan: Ahmad Fauzi',
                    'body' => 'Ahmad Fauzi mengumpulkan tugas "Website Portfolio" di kelas XII RPL 1.',
                    'link' => '/assignments',
                    'ago' => 5,
                ],
                [
                    'type' => Notification::TYPE_SUBMISSION_RECEIVED,
                    'title' => 'Tugas Dikumpulkan: Siti Nurhaliza',
                    'body' => 'Siti Nurhaliza mengumpulkan tugas "ERD Perpustakaan".',
                    'link' => '/assignments',
                    'ago' => 30,
                ],
                [
                    'type' => Notification::TYPE_ANNOUNCEMENT,
                    'title' => 'Pengumuman Admin: Rapat Guru Jumat',
                    'body' => 'Rapat evaluasi semester akan diadakan Jumat pukul 14.00 WIB.',
                    'link' => '/announcements',
                    'ago' => 240,
                ],
                [
                    'type' => Notification::TYPE_SUBMISSION_RECEIVED,
                    'title' => 'Tugas Dikumpulkan: Rizky Pratama',
                    'body' => 'Rizky mengumpulkan tugas "Laporan Praktikum Basis Data".',
                    'link' => '/assignments',
                    'ago' => 1440,
                    'read' => true,
                ],
                [
                    'type' => Notification::TYPE_ANNOUNCEMENT,
                    'title' => 'Pengumuman Admin: Libur Nasional',
                    'body' => 'Sekolah libur 1 Mei 2026 (Hari Buruh Internasional).',
                    'link' => '/announcements',
                    'ago' => 2880,
                    'read' => true,
                ],
            ];

            foreach ($items as $item) {
                Notification::create([
                    'account_id' => $acc->uuid,
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'body' => $item['body'],
                    'link' => $item['link'],
                    'data' => ['icon' => Notification::$icons[$item['type']] ?? '🔔'],
                    'read_at' => ! empty($item['read']) ? $now->copy()->subMinutes($item['ago']) : null,
                    'created_at' => $now->copy()->subMinutes($item['ago']),
                    'updated_at' => $now->copy()->subMinutes($item['ago']),
                ]);
            }
        }
    }
}
