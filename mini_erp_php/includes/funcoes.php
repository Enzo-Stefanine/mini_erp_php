<?php

function lerTerminal(string $mensagem): string {
    echo $mensagem;
    $entrada = fgets(STDIN);
    return trim($entrada);
}

function carregarProdutos(string $caminhoArquivo): array {
    if (!file_exists($caminhoArquivo)) {
        return [];
    }

    $conteudo = file_get_contents($caminhoArquivo);
    $dados = json_decode($conteudo, true);

    if (!is_array($dados)) {
        return [];
    }

    $produtos = [];
    foreach ($dados as $item) {
        $produtos[] = Produto::fromArray($item);
    }

    return $produtos;
}

function salvarProdutos(string $caminhoArquivo, array $produtos): void {
    $dir = dirname($caminhoArquivo);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $dados = [];
    foreach ($produtos as $p) {
        if ($p instanceof Produto) {
            $dados[] = $p->toArray();
        }
    }

    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($caminhoArquivo, $json);
}

function salvarVendas(string $caminhoArquivo, array $vendas): void {
    $dir = dirname($caminhoArquivo);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $dados = [];
    foreach ($vendas as $v) {
        if ($v instanceof Venda) {
            $dados[] = $v->toArray();
        }
    }

    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($caminhoArquivo, $json);
}

function carregarVendas(string $caminhoArquivo): array {
    if (!file_exists($caminhoArquivo)) {
        return [];
    }

    $conteudo = file_get_contents($caminhoArquivo);
    $dados = json_decode($conteudo, true);

    return is_array($dados) ? $dados : [];
}
