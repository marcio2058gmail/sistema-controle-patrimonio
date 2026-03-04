<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResponsibilityRequest;
use App\Http\Requests\UpdateResponsibilityRequest;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\Responsibility;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ResponsibilityController extends Controller
{
    public function index(): View
    {
        $responsibilities = Responsibility::with(['employee', 'asset'])
            ->latest()
            ->paginate(15);

        return view('responsibilities.index', compact('responsibilities'));
    }

    public function create(): View
    {
        $employees = Employee::orderBy('nome')->get();
        $assets  = Asset::disponivel()->orderBy('descricao')->get();

        return view('responsibilities.create', compact('employees', 'assets'));
    }

    public function store(StoreResponsibilityRequest $request): RedirectResponse
    {
        $responsibility = Responsibility::create($request->validated());

        // Marcar patrimônio como em uso
        $responsibility->asset->update(['status' => Asset::STATUS_IN_USE]);

        return redirect()->route('responsibilities.index')
            ->with('sucesso', 'Responsabilidade registrada com sucesso.');
    }

    public function show(Responsibility $responsibility): View
    {
        $responsibility->load(['employee', 'asset']);
        return view('responsibilities.show', compact('responsibility'));
    }

    public function edit(Responsibility $responsibility): View
    {
        $responsibility->load(['employee', 'asset']);
        return view('responsibilities.edit', compact('responsibility'));
    }

    public function update(UpdateResponsibilityRequest $request, Responsibility $responsibility): RedirectResponse
    {
        $responsibility->update($request->validated());

        // Se foi devolvido, marcar patrimônio como disponível
        if ($request->filled('data_devolucao') && ! $responsibility->asset->activeResponsibility()) {
            $responsibility->asset->update(['status' => Asset::STATUS_AVAILABLE]);
        }

        return redirect()->route('responsibilities.index')
            ->with('sucesso', 'Responsabilidade atualizada com sucesso.');
    }

    public function destroy(Responsibility $responsibility): RedirectResponse
    {
        $responsibility->delete();

        return redirect()->route('responsibilities.index')
            ->with('sucesso', 'Responsabilidade removida.');
    }

    public function gerarPdf(Responsibility $responsibility): Response
    {
        $responsibility->load(['employee', 'asset']);

        $pdf = Pdf::loadView('responsibilities.pdf', compact('responsibility'))
            ->setPaper('a4', 'portrait');

        $nomeArquivo = "termo-responsabilidade-{$responsibility->id}.pdf";

        return $pdf->download($nomeArquivo);
    }
}
