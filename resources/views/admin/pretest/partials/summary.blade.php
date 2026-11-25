<div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
    <x-stat-card label="Total Soal" :value="$stats['total']" note="Urutan soal pre-test" icon="doc" tone="sky" />
    <x-stat-card label="Soal Berkode" :value="$stats['with_code']" note="Dilengkapi potongan kode" icon="sparkles" tone="lime" />
    <x-stat-card label="Bahasa" :value="$stats['languages']" note="Variasi bahasa pemrograman" icon="chart" tone="indigo" />
</div>
