<?php
$produtos = [];
$csv_file = __DIR__ . '/data/produtos.csv';

if (file_exists($csv_file) && ($handle = fopen($csv_file, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); // Descarta cabeçalho
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) < 6) continue;
        $produtos[] = [
            "nome"      => trim($data[0]),
            "descricao" => trim($data[1]),
            "preco"     => floatval(str_replace(',', '.', $data[2])),
            "imagem"    => trim($data[3]),
            "categoria" => trim($data[4]),
            "destaque"  => trim($data[5])
        ];
    }
    fclose($handle);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($produtos);