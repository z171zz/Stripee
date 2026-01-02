<?php
error_reporting(0);
header('Content-Type: application/json');

// send.php NÃO FAZ NADA COM OS CARTÕES
// Apenas retorna OK para não quebrar o frontend
// O envio para Discord acontece APENAS em api.php quando a CC é APROVADA

if (isset($_POST['cartoes']) && !empty($_POST['cartoes'])) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error']);
}

?>
