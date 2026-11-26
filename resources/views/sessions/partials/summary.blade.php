<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <x-stat-card label="Total Sesi" :value="$stats['total']" note="Seluruh pertemuan terdaftar" icon="calendar" tone="sky" />
    <x-stat-card label="Sesi Aktif" :value="$stats['active']" note="QR presensi berjalan" icon="qr" tone="lime" />
    <x-stat-card label="Sesi Selesai" :value="$stats['finished']" note="Sudah ditutup asisten" icon="check" tone="emerald" />
    <x-stat-card label="Total Presensi" :value="$stats['attendance']" note="Scan kehadiran mahasiswa" icon="users" tone="indigo" />
</div>
