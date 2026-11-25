<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <x-stat-card label="Total Tugas" :value="$stats['total']" note="Tugas mingguan terunggah" icon="upload" tone="sky" />
    <x-stat-card label="Masih Dibuka" :value="$stats['open']" note="Deadline belum lewat" icon="clock" tone="lime" />
    <x-stat-card label="Ditutup" :value="$stats['closed']" note="Lewat batas pengumpulan" icon="x" tone="rose" />
    <x-stat-card label="Pengumpulan" :value="$stats['submissions']" note="PDF mahasiswa masuk" icon="doc" tone="emerald" />
</div>
