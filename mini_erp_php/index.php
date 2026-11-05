<?php

require_once __DIR__ . '/interfaces/Vendavel.php';
require_once __DIR__ . '/classes/Produto.php';
require_once __DIR__ . '/classes/Servico.php';
require_once __DIR__ . '/classes/Venda.php';
require_once __DIR__ . '/includes/funcoes.php';

$arquivoEstoque = __DIR__ . '/dados/estoque.json';
$arquivoVendas  = __DIR__ . '/dados/vendas.json';

// Carrega produtos do JSON
/** @var Produto[] $produtos */
$produtos = carregarProdutos($arquivoEstoque);
$vendas = carregarVendas($arquivoVendas);

do {
    echo "
========================================
";
    echo "       SISTEMA MINI-ERP (PHP CLI)       
";
    echo "========================================
";
    echo "1. Cadastrar Novo Produto
";
    echo "2. Listar Produtos em Estoque
";
    echo "3. Realizar Nova Venda
";
    echo "4. Relatório de Vendas Realizadas
";
    echo "0. Sair
";
    echo "----------------------------------------
";
    
    $opcao = lerTerminal("Escolha uma opção: ");

    switch ($opcao) {
        case '1':
            echo "
--- CADASTRAR PRODUTO ---
";
            $nome = lerTerminal("Nome do produto: ");
            $preco = (float) lerTerminal("Preço unitário (R$): ");
            $estoque = (int) lerTerminal("Quantidade em estoque: ");

            // Gerar ID simples baseado no maior existente
            $novoId = 1;
            foreach ($produtos as $p) {
                if ($p->getId() >= $novoId) {
                    $novoId = $p->getId() + 1;
                }
            }

            $novoProduto = new Produto($novoId, $nome, $preco, $estoque);
            $produtos[] = $novoProduto;

            salvarProdutos($arquivoEstoque, $produtos);
            echo "✔ Produto cadastrado e salvo com sucesso! (ID: $novoId)
";
            break;

        case '2':
            echo "
--- ESTOQUE DE PRODUTOS ---
";
            if (empty($produtos)) {
                echo "Nenhum produto cadastrado.
";
            } else {
                printf("%-5s | %-30s | %-12s | %-8s
", "ID", "Nome", "Preço (R$)", "Estoque");
                echo str_repeat("-", 65) . "
";
                foreach ($produtos as $p) {
                    printf("%-5d | %-30s | R$ %-9.2f | %-8d
", $p->getId(), $p->getNome(), $p->getPreco(), $p->getEstoque());
                }
            }
            break;

        case '3':
            echo "
--- NOVA VENDA ---
";
            if (empty($produtos)) {
                echo "❌ Não há produtos no estoque para vender.
";
                break;
            }

            $idVenda = count($vendas) + 1;
            $venda = new Venda($idVenda);

            while (true) {
                echo "
Opções do Carrinho:
";
                echo "  1. Adicionar Produto
";
                echo "  2. Adicionar Serviço
";
                echo "  0. Finalizar / Processar Venda
";
                $tipoItem = lerTerminal("Opção: ");

                if ($tipoItem === '1') {
                    echo "
Produtos disponíveis:
";
                    foreach ($produtos as $p) {
                        echo "  [ID: {$p->getId()}] {$p->getNome()} - R$ " . number_format($p->getPreco(), 2) . " (Estoque: {$p->getEstoque()})
";
                    }

                    $idProd = (int) lerTerminal("Digite o ID do produto: ");
                    $prodSelecionado = null;
                    foreach ($produtos as $p) {
                        if ($p->getId() === $idProd) {
                            $prodSelecionado = $p;
                            break;
                        }
                    }

                    if (!$prodSelecionado) {
                        echo "❌ Produto não encontrado!
";
                        continue;
                    }

                    $qtd = (int) lerTerminal("Quantidade desejada: ");
                    if ($qtd <= 0) {
                        echo "❌ Quantidade inválida!
";
                        continue;
                    }

                    if ($prodSelecionado->darBaixaEstoque($qtd)) {
                        $venda->adicionarItem($prodSelecionado, $qtd);
                        echo "✔ Produto adicionado ao carrinho!
";
                    } else {
                        echo "❌ Quantidade insuficiente em estoque!
";
                    }

                } elseif ($tipoItem === '2') {
                    $nomeServ = lerTerminal("Descrição do serviço: ");
                    $valorHora = (float) lerTerminal("Valor da hora (R$): ");
                    $horas = (float) lerTerminal("Horas estimadas: ");

                    $servico = new Servico(rand(100, 999), $nomeServ, $valorHora, $horas);
                    $venda->adicionarItem($servico, 1);
                    echo "✔ Serviço adicionado ao carrinho!
";

                } elseif ($tipoItem === '0') {
                    break;
                } else {
                    echo "❌ Opção inválida!
";
                }
            }

            if (count($venda->getItens()) > 0) {
                // Salva alterações do estoque e grava a venda
                salvarProdutos($arquivoEstoque, $produtos);
                
                $vendasObj = [];
                // Recarrega ou cria a lista para salvar
                $vendasHist = carregarVendas($arquivoVendas);
                $vendasHist[] = $venda->toArray();
                
                $dir = dirname($arquivoVendas);
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                file_put_contents($arquivoVendas, json_encode($vendasHist, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                echo "
========================================
";
                echo "🎉 Venda #{$venda->getId()} finalizada com sucesso!
";
                echo "Total: R$ " . number_format($venda->calcularTotal(), 2, ',', '.') . "
";
                echo "========================================
";
            } else {
                echo "⚠️ Venda cancelada (carrinho vazio).
";
            }
            break;

        case '4':
            echo "
--- RELATÓRIO DE VENDAS ---
";
            $vendasHist = carregarVendas($arquivoVendas);
            if (empty($vendasHist)) {
                echo "Nenhuma venda registrada até o momento.
";
            } else {
                foreach ($vendasHist as $v) {
                    echo "
----------------------------------------
";
                    echo "Venda ID: {$v['id']} | Data: {$v['data']}
";
                    echo "Itens:
";
                    foreach ($v['itens'] as $it) {
                        echo "  - [{$it['tipo']}] {$it['nome']} x{$it['quantidade']} = R$ " . number_format($it['subtotal'], 2, ',', '.') . "
";
                    }
                    echo "Total da Venda: R$ " . number_format($v['total'], 2, ',', '.') . "
";
                }
            }
            break;

        case '0':
            echo "
Saindo do sistema... Até logo!
";
            break;

        default:
            echo "❌ Opção inválida! Tente novamente.
";
            break;
    }

} while ($opcao !== '0');
