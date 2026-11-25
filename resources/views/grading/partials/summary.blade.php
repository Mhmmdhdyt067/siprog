<div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
    <x-stat-card label="Mahasiswa" :value="$stats['students']" note="Peserta kelas ini" icon="users" tone="sky" />
    <x-stat-card label="Sudah Dinilai" :value="$stats['graded']" note="Nilai tersimpan" icon="check" tone="emerald" />
    <x-stat-card label="Belum Dinilai" :value="$stats['pending']" note="Menunggu input" icon="clock" tone="amber" />
    <x-stat-card label="Hadir" :value="$stats['attendance']" note="Presensi terverifikasi" icon="qr" tone="lime" />
    <x-stat-card label="Rata-Rata" :value="number_format($stats['average'], 2)" note="Dari nilai terisi" icon="chart" tone="indigo" />
</div>
