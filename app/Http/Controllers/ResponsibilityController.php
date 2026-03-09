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
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResponsibilityController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $query = Responsibility::with(['employee', 'assets'])->latest();

        if (! $user->isAdmin()) {
            $employeeId = $user->employee?->id;
            abort_unless($employeeId, 403);
            $query->where('funcionario_id', $employeeId);
        } else {
            // Scope por empresa
            $companyId = (int) session('empresa_ativa_id');
            if ($companyId) {
                $empleadosIds = \App\Models\Employee::forCompany($companyId)->pluck('id');
                $query->whereIn('funcionario_id', $empleadosIds);
            }
        }

        $responsibilities = $query->paginate(15);
        $employees        = \App\Models\Employee::forCompany()->orderBy('nome')->get();
        $availableAssets  = \App\Models\Asset::forCompany()->disponivel()->orderBy('descricao')->get();

        return view('responsibilities.index', compact('responsibilities', 'employees', 'availableAssets'));
    }

    public function create(): View
    {
        $employees = Employee::forCompany()->orderBy('nome')->get();
        $assets    = Asset::forCompany()->disponivel()->orderBy('descricao')->get();

        return view('responsibilities.create', compact('employees', 'assets'));
    }

    public function store(StoreResponsibilityRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('patrimonio_ids');
        $patrimonioIds = $request->input('patrimonio_ids', []);

        $responsibility = Responsibility::create($data);
        $responsibility->assets()->attach($patrimonioIds);

        // Marcar todos os patrimônios como em uso
        Asset::whereIn('id', $patrimonioIds)->update(['status' => Asset::STATUS_IN_USE]);

        $count = count($patrimonioIds);
        return redirect()->route('responsibilities.index')
            ->with('sucesso', "Termo registrado com {$count} equipamento(s).");
    }

    public function show(Responsibility $responsibility): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort_unless(
                $responsibility->funcionario_id === $user->employee?->id,
                403
            );
        }
        $responsibility->load(['employee.company', 'employee.department', 'assets']);
        return view('responsibilities.show', compact('responsibility'));
    }

    public function edit(Responsibility $responsibility): View
    {
        $responsibility->load(['employee', 'assets']);
        $assets = Asset::disponivel()->orderBy('descricao')->get();
        return view('responsibilities.edit', compact('responsibility', 'assets'));
    }

    public function update(UpdateResponsibilityRequest $request, Responsibility $responsibility): RedirectResponse
    {
        $responsibility->update($request->safe()->except('patrimonio_ids'));

        // Anexar novos patrimônios se enviados
        if ($request->has('patrimonio_ids')) {
            $newIds = array_filter((array) $request->input('patrimonio_ids'));
            if (! empty($newIds)) {
                $existingIds = $responsibility->assets()->pluck('patrimonios.id')->toArray();
                $toAttach = array_diff($newIds, $existingIds);
                if (! empty($toAttach)) {
                    $responsibility->assets()->attach($toAttach);
                    \App\Models\Asset::whereIn('id', $toAttach)->update(['status' => \App\Models\Asset::STATUS_IN_USE]);
                }
            }
        }

        // Se data_devolucao foi preenchida, liberar patrimônios que não têm outro termo ativo
        if ($request->filled('data_devolucao')) {
            foreach ($responsibility->assets as $asset) {
                $outroAtivo = $asset->responsibilities()
                    ->where('termos.id', '!=', $responsibility->id)
                    ->whereNull('data_devolucao')
                    ->exists();

                if (! $outroAtivo) {
                    $asset->update(['status' => Asset::STATUS_AVAILABLE]);
                }
            }
        }

        return redirect()->route('responsibilities.index')
            ->with('sucesso', 'Termo atualizado com sucesso.');
    }

    public function destroy(Responsibility $responsibility): RedirectResponse
    {
        $responsibility->load('assets');

        // Se o termo estava ativo, liberar os patrimônios sem outro termo ativo
        if (! $responsibility->data_devolucao) {
            foreach ($responsibility->assets as $asset) {
                $outroAtivo = $asset->responsibilities()
                    ->where('termos.id', '!=', $responsibility->id)
                    ->whereNull('data_devolucao')
                    ->exists();

                if (! $outroAtivo) {
                    $asset->update(['status' => Asset::STATUS_AVAILABLE]);
                }
            }
        }

        $responsibility->delete();

        return redirect()->route('responsibilities.index')
            ->with('sucesso', 'Termo removido.');
    }

    public function assinar(\Illuminate\Http\Request $request, Responsibility $responsibility): RedirectResponse
    {
        $user = $request->user();

        // Somente o responsável pelo termo (funcionário ou gestor) pode assinar
        abort_unless($user->employee?->id === $responsibility->funcionario_id, 403);

        if ($responsibility->assinado) {
            return back()->with('erro', 'Este termo já foi assinado.');
        }

        $request->validate([
            'assinatura_base64' => ['required', 'string'],
        ]);

        $responsibility->update([
            'assinatura_base64' => $request->assinatura_base64,
            'assinado'          => true,
            'assinado_em'       => now(),
            'assinado_ip'       => $request->ip(),
        ]);

        return redirect()->route('responsibilities.show', $responsibility)
            ->with('sucesso', 'Termo assinado com sucesso! Agora você pode baixar o PDF.');
    }

    public function devolver(\Illuminate\Http\Request $request, Responsibility $responsibility): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($responsibility->data_devolucao !== null, 422, 'Este termo já possui devolução registrada.');

        $request->validate([
            'data_devolucao'      => ['required', 'date', 'after_or_equal:' . $responsibility->data_entrega->toDateString(), 'before_or_equal:today'],
            'observacao_devolucao' => ['nullable', 'string', 'max:1000'],
        ], [
            'data_devolucao.required'          => 'A data de devolução é obrigatória.',
            'data_devolucao.after_or_equal'    => 'A data de devolução deve ser igual ou posterior à data de entrega (' . $responsibility->data_entrega->format('d/m/Y') . ').',
            'data_devolucao.before_or_equal'   => 'A data de devolução não pode ser futura.',
        ]);

        $responsibility->update([
            'data_devolucao'       => $request->data_devolucao,
            'observacao_devolucao' => $request->observacao_devolucao,
        ]);

        // Libera os equipamentos que não estão em outro termo ativo
        $responsibility->load('assets');
        foreach ($responsibility->assets as $asset) {
            $outroAtivo = $asset->responsibilities()
                ->where('termos.id', '!=', $responsibility->id)
                ->whereNull('data_devolucao')
                ->exists();

            if (! $outroAtivo) {
                $asset->update(['status' => Asset::STATUS_AVAILABLE]);
            }
        }

        return redirect()->route('responsibilities.show', $responsibility)
            ->with('sucesso', 'Devolução registrada com sucesso. Equipamento(s) liberado(s).');
    }

    public function gerarPdf(Responsibility $responsibility): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort_unless(
                $responsibility->funcionario_id === $user->employee?->id && $responsibility->assinado,
                403
            );
        }

        $responsibility->load(['employee.company', 'employee.department', 'assets']);

        $company = $responsibility->employee->company;

        $template = $company?->modelo_pdf ?? 'padrao';
        $viewName = "responsibilities.templates.{$template}";
        if (!view()->exists($viewName)) {
            $viewName = 'responsibilities.templates.padrao';
        }

        $pdf = Pdf::loadView($viewName, compact('responsibility', 'company'))
            ->setPaper('a4', 'portrait');

        $nomeArquivo = "termo-responsabilidade-{$responsibility->id}.pdf";

        return $pdf->download($nomeArquivo);
    }
}

