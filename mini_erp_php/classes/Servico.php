<?php

require_once __DIR__ . '/../interfaces/Vendavel.php';

class Servico implements Vendavel {
    private int $id;
    private string $nome;
    private float $valorHora;
    private float $horasEstimadas;

    public function __construct(int $id, string $nome, float $valorHora, float $horasEstimadas) {
        $this->id = $id;
        $this->nome = $nome;
        $this->valorHora = $valorHora;
        $this->horasEstimadas = $horasEstimadas;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getPreco(): float {
        return $this->valorHora * $this->horasEstimadas;
    }

    public function getValorHora(): float {
        return $this->valorHora;
    }

    public function getHorasEstimadas(): float {
        return $this->horasEstimadas;
    }

    public function getTipo(): string {
        return "Servico";
    }
}
