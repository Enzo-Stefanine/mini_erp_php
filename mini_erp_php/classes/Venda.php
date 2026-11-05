<?php

require_once __DIR__ . '/../interfaces/Vendavel.php';

class Venda {
    private int $id;
    private array $itens = [];
    private string $data;

    public function __construct(int $id) {
        $this->id = $id;
        $this->data = date('Y-m-d H:i:s');
    }

    public function getId(): int {
        return $this->id;
    }

    public function getData(): string {
        return $this->data;
    }

    public function adicionarItem(Vendavel $item, int $qtd = 1): void {
        $this->itens[] = [
            'item' => $item,
            'quantidade' => $qtd,
            'subtotal' => $item->getPreco() * $qtd
        ];
    }

    public function getItens(): array {
        return $this->itens;
    }

    public function calcularTotal(): float {
        $total = 0.0;
        foreach ($this->itens as $linha) {
            $total += $linha['subtotal'];
        }
        return $total;
    }

    public function toArray(): array {
        $itensFormatados = [];
        foreach ($this->itens as $linha) {
            /** @var Vendavel $item */
            $item = $linha['item'];
            $itensFormatados[] = [
                'id' => $item->getId(),
                'nome' => $item->getNome(),
                'tipo' => $item->getTipo(),
                'preco_unitario' => $item->getPreco(),
                'quantidade' => $linha['quantidade'],
                'subtotal' => $linha['subtotal']
            ];
        }

        return [
            'id' => $this->id,
            'data' => $this->data,
            'total' => $this->calcularTotal(),
            'itens' => $itensFormatados
        ];
    }
}
