<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

/**
 * InventoryService — gerencia o ciclo de vida de inventários.
 */
class InventoryService
{
    /**
     * Abre um novo inventário para a empresa ativa.
     * Popula todos os patrimônios da empresa como itens pendentes.
     */
    public function open(int $empresaId, string $descricao = ''): Inventory
    {
        return DB::transaction(function () use ($empresaId, $descricao) {
            $inventory = Inventory::create([
                'empresa_id'  => $empresaId,
                'descricao'   => $descricao,
                'status'      => Inventory::STATUS_IN_PROGRESS,
                'iniciado_em' => now(),
            ]);

            // Obtém todos os patrimônios da empresa sem passar pelo Global Scope
            $assets = Asset::withAllCompanies()
                ->where('empresa_id', $empresaId)
                ->pluck('id');

            $items = $assets->map(fn ($assetId) => [
                'empresa_id'    => $empresaId,
                'inventario_id' => $inventory->id,
                'patrimonio_id' => $assetId,
                'status'        => InventoryItem::STATUS_PENDING,
                'created_at'    => now(),
                'updated_at'    => now(),
            ])->all();

            InventoryItem::insert($items);

            return $inventory->load('items');
        });
    }

    /**
     * Registra o status de um item durante o inventário.
     */
    public function checkItem(InventoryItem $item, string $status, ?string $observacao = null): InventoryItem
    {
        $item->update([
            'status'     => $status,
            'observacao' => $observacao,
        ]);

        return $item;
    }

    /**
     * Finaliza o inventário.
     */
    public function close(Inventory $inventory): Inventory
    {
        $inventory->update([
            'status'        => Inventory::STATUS_FINISHED,
            'finalizado_em' => now(),
        ]);

        return $inventory;
    }
}
