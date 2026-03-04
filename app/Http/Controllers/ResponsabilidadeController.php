<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResponsabilidadeRequest;
use App\Http\Requests\UpdateResponsabilidadeRequest;
use App\Models\Funcionario;
use App\Models\Patrimonio;
use App\Models\Responsabilidade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ResponsabilidadeController extends Controller
{
    public function index(): View
    {
        $responsabilidades = Responsabilidade::with(['funcionario', 'patrimonio'])
            ->latest()
            ->paginate(15);

        return view('responsabilidades.index', compact('responsabilidades'));
    }

    public function create(): View
    {
        $funcionarios = Funcionario::orderBy('nome')->get();
        $patrimonios  = Patrimonio::disponivel()->orderBy('descricao')->get();

        return view('responsabilidades.create', compact('funcionarios', 'patrimonios'));
    }

    public function store(StoreResponsabilidadeRequest $request): RedirectResponse
    {
        $responsabilidade = Responsabilidade::create($request->validated());

        // Marcar patrimônio como em uso
        $responsabilidade->patrimonio->update(['status' => Patrimonio::STATUS_EM_USO]);

        return redirect()->route('responsabilidades.index')
            ->with('sucesso', 'Responsabilidade registrada com sucesso.');
    }

    public function show(Responsabilidade $responsabilidade): View
    {
        $responsabilidade->load(['funcionario', 'patrimonio']);
        return view('responsabilidades.show', compact('responsabilidade'));
    }

    public function edit(Responsabilidade $responsabilidade): View
    {
        $responsabilidade->load(['funcionario', 'patrimonio']);
        return view('responsabilidades.edit', compact('responsabilidade'));
    }

    public function update(UpdateResponsabilidadeRequest $request, Responsabilidade $responsabilidade): RedirectResponse
    {
        $responsabilidade->update($request->validated());

        // Se foi devolvido, marcar patrimônio como disponível
        if ($request->filled('data_devolucao') && ! $responsabilidade->patrimonio->responsabilidadeAtiva()) {
            $responsabilidade->patrimonio->update(['status' => Patrimonio::STATUS_DISPONIVEL]);
        }

        return redirect()->route('responsabilidades.index')
            ->with('sucesso', 'Responsabilidade atualizada com sucesso.');
    }

    public function destroy(Responsabilidade $responsabilidade): RedirectResponse
    {
        $responsabilidade->delete();

        return redirect()->route('responsabilidades.index')
            ->with('sucesso', 'Responsabilidade removida.');
    }

    public function gerarPdf(Responsabilidade $responsabilidade): Response
    {
        $responsabilidade->load(['funcionario', 'patrimonio']);

        $pdf = Pdf::loadView('responsabilidades.pdf', compact('responsabilidade'))
            ->setPaper('a4', 'portrait');

        $nomeArquivo = "termo-responsabilidade-{$responsabilidade->id}.pdf";

        return $pdf->download($nomeArquivo);
    }
}
