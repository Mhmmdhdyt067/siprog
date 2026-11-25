<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <x-stat-card label="Total Kelas" :value="$stats['classrooms']" note="Kelas praktikum" icon="building" tone="sky" />
    <x-stat-card label="Mahasiswa" :value="$stats['students']" note="Terdaftar di kelas" icon="users" tone="lime" />
    <x-stat-card label="Asisten" :value="$stats['assistants']" note="Pengampu kelas" icon="badge" tone="indigo" />
    <x-stat-card label="Sesi" :value="$stats['sessions']" note="Pertemuan terjadwal" icon="calendar" tone="emerald" />
</div>
