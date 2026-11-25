<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <x-stat-card label="Total Info" :value="$stats['total']" note="Pemberitahuan terpublikasi" icon="megaphone" tone="sky" />
    <x-stat-card label="Info Umum" :value="$stats['info']" note="Pengumuman biasa" icon="sparkles" tone="lime" />
    <x-stat-card label="Jadwal" :value="$stats['jadwal']" note="Perubahan jadwal" icon="calendar" tone="indigo" />
    <x-stat-card label="Tugas" :value="$stats['tugas']" note="Notifikasi tugas baru" icon="upload" tone="amber" />
</div>
