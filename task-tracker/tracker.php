<?php
$arquivo = __DIR__ . "/tasks.json";

function carregarTarefas($arquivo){
    if(!file_exists($arquivo)) return [];
    return json_decode(file_get_contents($arquivo), true) ?? [];
}

function salvarTarefas($arquivo,$dados){
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT));
}

$comando = $argv[1] ?? null;      // Ex: add, list, delete...
$param1  = $argv[2] ?? null;      // Ex: descrição ou id
$param2  = $argv[3] ?? null;      // Ex: nova descrição
$tarefas = carregarTarefas($arquivo);

switch($comando){
    case "add":
        if(!$param1){ echo "Uso: php task.php add \"descrição\"\n"; break; }
        $id = count($tarefas)+1;
        $tarefas[] = ["id"=>$id,"descricao"=>$param1,"status"=>"todo"];
        salvarTarefas($arquivo,$tarefas);
        echo "Tarefa $id adicionada.\n";
        break;

    case "list":
        foreach($tarefas as $t){
            echo "[{$t['id']}] {$t['descricao']} ({$t['status']})\n";
        }
        if(empty($tarefas)) echo "Nenhuma tarefa.\n";
        break;

    case "mark-done":
        if(!$param1){ echo "Uso: php task.php mark-done <id>\n"; break; }
        foreach($tarefas as &$t){
            if($t['id']==$param1){ $t['status']="done"; }
        }
        salvarTarefas($arquivo,$tarefas);
        echo "Tarefa $param1 marcada como concluída.\n";
        break;

    case "delete":
        if(!$param1){ echo "Uso: php task.php delete <id>\n"; break; }
        $tarefas = array_filter($tarefas, fn($t)=>$t['id']!=$param1);
        salvarTarefas($arquivo,$tarefas);
        echo "Tarefa $param1 deletada.\n";
        break;

    default:
        echo "Comandos:\n";
        echo "  php task.php add \"descrição\"\n";
        echo "  php task.php list\n";
        echo "  php task.php mark-done <id>\n";
        echo "  php task.php delete <id>\n";
}
