<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faq.index', ['faqs' => Faq::orderBy('urutan')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('admin.faq.form', ['faq' => new Faq(['aktif' => true, 'urutan' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $faq = Faq::create($this->validasi($request));
        ActivityLog::catat('membuat', 'FAQ #'.$faq->id, $faq->pertanyaan);

        return redirect()->route('admin.faq.index')->with('sukses', 'FAQ dibuat.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faq.form', ['faq' => $faq]);
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validasi($request));
        ActivityLog::catat('mengubah', 'FAQ #'.$faq->id, $faq->pertanyaan);

        return redirect()->route('admin.faq.index')->with('sukses', 'FAQ diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $pertanyaan = $faq->pertanyaan;
        $faq->delete();
        ActivityLog::catat('menghapus', 'FAQ', $pertanyaan);

        return back()->with('sukses', 'FAQ dihapus.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:200'],
            'jawaban'    => ['required', 'string'],
            'kata_kunci' => ['required', 'string', 'max:255'],
            'kategori'   => ['nullable', 'string', 'max:60'],
            'urutan'     => ['nullable', 'integer', 'min:0'],
        ]);
        $data['urutan'] = $data['urutan'] ?? 0;
        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }
}
