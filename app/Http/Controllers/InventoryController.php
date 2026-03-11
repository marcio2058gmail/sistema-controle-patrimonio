<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * InventoryController — gerencia inventários de patrimônios por empresa.
 * Acesso restrito a Admin e Manager da empresa.
 */
class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    /**
     * Lista inventários da empresa ativa.
     *
     * O Global Scope (BelongsToCompany) já filtra por empresa_id automaticamente.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isAdminOrManager(), 403);

        // Sem necessidade de ->where('empresa_id', ...) — o Global Scope já aplica.
        $inventories = Inventory::with('items')
            ->latest('iniciado_em')
            ->paginate(10);

        return view('inventories.index', compact('inventories'));
    }

    /**
     * Abre um novo inventário para a empresa ativa.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isAdminOrManager(), 403);

        $data = $request->validate([
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        $empresaId = (int) session('empresa_ativa_id');

        $inventory = $this->inventoryService->open(
            empresaId: $empresaId,
            descricao: $data['descricao'] ?? '',
        );

        return redirect()->route('inventories.show', $inventory)
            ->with('sucesso', 'Inventário aberto com sucesso.');
    }

    /**
     * Exibe os itens de um inventário.
     */
    public function show(Request $request, Inventory $inventory): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isAdminOrManager(), 403);

        $inventory->load(['items.asset']);

        $statusLabels = InventoryItem::statusLabels();

        return view('inventories.show', compact('inventory', 'statusLabels'));
    }

    /**
     * Atualiza o status de um item do inventário.
     */
    public function updateItem(Request $request, InventoryItem $item): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isAdminOrManager(), 403);

        $data = $request->validate([
            'status'     => ['required', 'in:encontrado,nao_encontrado,avariado,pendente'],
            'observacao' => ['nullable', 'string', 'max:500'],
        ]);

        $this->inventoryService->checkItem($item, $data['status'], $data['observacao'] ?? null);

        return back()->with('sucesso', 'Item atualizado.');
    }

    /**
     * Finaliza um inventário em andamento.
     */
    public function close(Request $request, Inventory $inventory): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isAdminOrManager(), 403);

        if ($inventory->status !== Inventory::STATUS_IN_PROGRESS) {
            return back()->with('erro', 'Este inventário já foi finalizado ou cancelado.');
        }

        $this->inventoryService->close($inventory);

        return redirect()->route('inventories.show', $inventory)
            ->with('sucesso', 'Inventário finalizado.');
    }
}
