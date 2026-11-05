<?php

interface Vendavel {
    public function getId(): int;
    public function getNome(): string;
    public function getPreco(): float;
    public function getTipo(): string;
}
