<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\AssignmentReviewController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\MasterUserController;
use App\Http\Controllers\Admin\PretestMonitorController;
use App\Http\Controllers\Admin\PretestQuestionController;
use App\Http\Controllers\AssignmentFileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\Mahasiswa\FeedbackController;
use App\Http\Controllers\Mahasiswa\NilaiController;
use App\Http\Controllers\Mahasiswa\PresensiController;
use App\Http\Controllers\Mahasiswa\PretestController as MahasiswaPretestController;
use App\Http\Controllers\Mahasiswa\TugasController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

// Portal 1 Pintu (Public)
Route::get('/', [PortalController::class, 'index'])->name('portal');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/assignments/{assignment}/file', AssignmentFileController::class)->name('assignments.file');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Mahasiswa
    Route::middleware(['role:mahasiswa'])->name('mahasiswa.')->group(function () {
        Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
        Route::post('/presensi/scan', [PresensiController::class, 'scan'])->name('presensi.scan');

        Route::get('/pretest', [MahasiswaPretestController::class, 'index'])->name('pretest.index');
        Route::get('/pretest/{session}', [MahasiswaPretestController::class, 'show'])->name('pretest.show');
        Route::post('/pretest/{session}/finish', [MahasiswaPretestController::class, 'finish'])->name('pretest.finish');
        Route::get('/pretest/{session}/state', [MahasiswaPretestController::class, 'state'])->name('pretest.state');
        Route::post('/pretest/{session}/violation', [MahasiswaPretestController::class, 'violation'])->name('pretest.violation');

        Route::post('/feedback/{session}', [FeedbackController::class, 'store'])->name('feedback.store');

        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::post('/tugas/{assignment}/submit', [TugasController::class, 'submit'])->name('tugas.submit');

        Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    });

    // Penilaian (Asisten & Admin)
    Route::middleware(['role:asisten,admin'])->group(function () {
        Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
        Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
        Route::put('/sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
        Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
        Route::get('/sessions/{session}/qr', [SessionController::class, 'showQr'])->name('sessions.qr');
        Route::get('/sessions/{session}/attendees', [SessionController::class, 'attendees'])->name('sessions.attendees');
        Route::post('/sessions/{session}/qr/refresh', [SessionController::class, 'refreshQr'])->name('sessions.qr.refresh');
        Route::post('/sessions/{session}/finish', [SessionController::class, 'finish'])->name('sessions.finish');

        Route::get('/sessions/{session}/grading', [GradingController::class, 'index'])->name('grading.index');
        Route::post('/sessions/{session}/grading', [GradingController::class, 'store'])->name('grading.store');
        Route::post('/sessions/{session}/grading/{student}', [GradingController::class, 'storeStudent'])->name('grading.student');
        Route::get('/submissions/{submission}/view', [GradingController::class, 'downloadSubmission'])->name('submissions.view');
    });

    // Menu Khusus Admin (Data Master, Tugas, Pemberitahuan, & Export)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('classrooms', ClassroomController::class)->except(['create', 'show', 'edit']);

        Route::get('/master/students', [MasterUserController::class, 'students'])->name('master.students');
        Route::get('/master/assistants', [MasterUserController::class, 'assistants'])->name('master.assistants');
        Route::post('/master/users', [MasterUserController::class, 'store'])->name('master.users.store');
        Route::put('/master/users/{user}', [MasterUserController::class, 'update'])->name('master.users.update');
        Route::delete('/master/users/{user}', [MasterUserController::class, 'destroy'])->name('master.users.destroy');

        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::get('/grades', [ExportController::class, 'index'])->name('grades.index');
        Route::get('/export/session/{session}', [ExportController::class, 'exportBySession'])->name('export.session');
        Route::get('/export/overall', [ExportController::class, 'exportOverall'])->name('export.overall');
    });

    // Konten & tugas (Asisten untuk kelas yang diampu, Admin untuk semua)
    Route::middleware(['role:asisten,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/assignments', [AdminAssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments', [AdminAssignmentController::class, 'store'])->name('assignments.store');
        Route::put('/assignments/{assignment}', [AdminAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [AdminAssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::get('/submissions', [AssignmentReviewController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{assignment}', [AssignmentReviewController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{assignment}/students/{student}', [AssignmentReviewController::class, 'grade'])
            ->name('submissions.grade');

        Route::get('/pretest', [PretestQuestionController::class, 'overview'])->name('pretest.overview');
        Route::get('/pretest/monitor', [PretestMonitorController::class, 'index'])->name('pretest.monitor');
        Route::get('/pretest/monitor/data', [PretestMonitorController::class, 'data'])->name('pretest.monitor.data');
        Route::post('/pretest/monitor/{attendance}/warn', [PretestMonitorController::class, 'warn'])->name('pretest.monitor.warn');
        Route::post('/pretest/monitor/{attendance}/unlock', [PretestMonitorController::class, 'unlock'])->name('pretest.monitor.unlock');
        Route::get('/sessions/{session}/pretest', [PretestQuestionController::class, 'index'])->name('pretest.index');
        Route::post('/sessions/{session}/pretest', [PretestQuestionController::class, 'store'])->name('pretest.store');
        Route::put('/pretest/{question}', [PretestQuestionController::class, 'update'])->name('pretest.update');
        Route::delete('/pretest/{question}', [PretestQuestionController::class, 'destroy'])->name('pretest.destroy');
    });
});

require __DIR__.'/auth.php';
