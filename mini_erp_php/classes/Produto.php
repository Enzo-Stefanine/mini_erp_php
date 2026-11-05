<?php

require_once __DIR__ . '/../interfaces/Vendavel.php';

class Produto implements Vendavel {
    private int $id;
    private string $nome;
    private float $preco;
    private int $estoque;

    public function __construct(int $id, string $nome, float $preco, int $estoque) {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->estoque = $estoque;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getPreco(): float {
        return $this->preco;
    }

    public function getEstoque(): int {
        return $this->estoque;
    }

    public function getTipo(): string {
        return "Produto";
    }

    public function darBaixaEstoque(int $quantidade): bool {
        if ($quantidade <= 0) {
            return false;
        }
        if ($quantidade > $this->estoque) {
            return false;
        }
        $this->estoque -= $quantidade;
        return true;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'preco' => $this->preco,
            'estoque' => $this->estoque
        ];
    }

    public static function fromArray(array $dados): Produto {
        return new Produto(
            (int) $dados['id'],
            $dados['nome'],
            (float) $dados['preco'],
            (int) $dados['estoque']
        );
    }
}
