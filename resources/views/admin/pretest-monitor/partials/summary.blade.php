<div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
    <x-stat-card label="Sedang Mengerjakan" :value="$stats['active']" note="Praktikan di kelas Anda" icon="clock"
        tone="sky" />
    <x-stat-card label="Ada Pelanggaran" :value="$stats['flagged']" note="Pernah berpindah tab/aplikasi" icon="eye"
        tone="amber" />
    <x-stat-card label="Terkunci" :value="$stats['locked']" note="Akses pre-test ditutup" icon="x"
        tone="rose" />
</div>
