<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">Soal Pre-Test: Pertemuan {{ $session->meeting_number }}
            ({{ $session->classroom->name }})</h1>

        <form action="{{ route('admin.pretest.store', $session) }}" method="POST"
            class="p-6 rounded-[22px] bg-white border border-black/5 space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-slate-500">Urutan Soal</label>
                    <input type="number" name="order" value="{{ $questions->count() + 1 }}" min="1" required
                        class="w-full rounded-xl bg-[#F7F7F4] border-black/10 text-sm">
                </div>
                <div>
                    <label class="text-xs text-slate-500">Bahasa Kode Program</label>
                    <select name="code_language" class="w-full rounded-xl bg-[#F7F7F4] border-black/10 text-sm">
                        <option value="cpp">C++</option>
                        <option value="php">PHP</option>
                        <option value="python">Python</option>
                        <option value="java">Java</option>
                        <option value="javascript">JavaScript</option>
                        <option value="sql">SQL</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs text-slate-500">Pertanyaan Esai</label>
                <textarea name="question_text" rows="3" required class="w-full rounded-xl bg-[#F7F7F4] border-black/10 text-sm"></textarea>
            </div>
            <div>
                <label class="text-xs text-slate-500">Text Box Kode Program (Opsional)</label>
                <textarea name="code_snippet" rows="5" placeholder="// Tempel potongan kode program di sini..."
                    class="w-full font-mono rounded-xl bg-[#F7F7F4] border-black/10 text-[#0A66C8] text-sm"></textarea>
            </div>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 font-semibold text-sm">
                + Tambah Soal Pre-Test
            </button>
        </form>

        <div class="space-y-4">
            @foreach ($questions as $q)
                <div class="p-5 rounded-[22px] bg-white border border-black/5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-[#0A66C8]">Soal #{{ $q->order }}</span>
                        <form action="{{ route('admin.pretest.destroy', $q) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-rose-600 text-xs hover:underline">Hapus</button>
                        </form>
                    </div>
                    <p class="text-sm">{{ $q->question_text }}</p>
                    @if ($q->code_snippet)
                        <pre class="mt-3 rounded-xl overflow-hidden"><code class="language-{{ $q->code_language }}">{{ $q->code_snippet }}</code></pre>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
