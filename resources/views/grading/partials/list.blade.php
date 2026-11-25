<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[12px] sm:min-w-[64rem]">
            <thead class="table-head">
                <tr>
                    <th class="px-4 py-3">Mahasiswa</th>
                    <th class="px-4 py-3">Presensi</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Tugas PDF</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Pre-Test 40%</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Tugas 60%</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Tambahan</th>
                    <th class="px-4 py-3">Nilai Akhir</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($students as $mhs)
                    @include('grading.partials.row', ['mhs' => $mhs, 'session' => $session])
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <p class="text-[13px] font-bold">Belum ada mahasiswa di kelas ini</p>
                            <p class="mt-1 text-[11px] text-slate-500">Tambahkan mahasiswa melalui Data Master terlebih
                                dahulu.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
