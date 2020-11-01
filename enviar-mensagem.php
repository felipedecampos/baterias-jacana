<?php

/*\ Campos recebidos \*/
$campoNome = trim($_POST["name"]);
$campoEmail = trim($_POST["email"]);
$campoMensagem = trim($_POST["message"]);

if (empty($campoNome) || empty($campoEmail) || empty($campoMensagem)) {
    echo '
		<script>
			alert("Alguns campos do formulário não foram preenchidos!");
			window.history.back(-1);
		</script>
	';
    exit;
}

$ponteiro = fopen("email.txt", "r");
$dest = '';

while (!feof($ponteiro)) {
    $linha = fgets($ponteiro, 4096);
    $dest .= $linha;
}

fclose($ponteiro);

if (empty($dest)) {
    echo '
		<script>
			alert("Não foi possível localizar um destinatário!");
			window.history.back(-1);
		</script>
	';
    exit;
}

/*\ Mensagem à ser enviada \*/
$msg = "<p><font face='Verdana' size='1'><b>Nome:</b> \t$campoNome</font></p>";
$msg .= "<p><font face='Verdana' size='1'><b>E-mail:</b> \t$campoEmail</font></p>";
$msg .= "<p><font face='Verdana' size='1'><b>Mensagem:</b> \t$campoMensagem</font></p>";

/*\ Dados para envio da mensagem \*/
$mensagem = "$msg";
$destinatario = explode(",", trim($dest));

$assunto = "Contato do website www.bateriasjacana.com.br";

/*\ PHP MAILER \*/
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Charset UTF-8
    $mail->CharSet = "UTF-8";

    // Deixar linguagem em ptBR
    $mail->SetLanguage("br");

    //Informa que será utilizado o SMTP para envio do e-mail
    $mail->IsSMTP();

    //Informa que a conexão com o SMTP será autênticado
    $mail->SMTPAuth = true;

    //Configura a segurança para SSL
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    //Informa a porta de conexão do GAMIL
    $mail->Port = 465;

    //Informa o HOST do GMAIL
    $mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server

    //Usuário para autênticação do SMTP
    $mail->Username = "felipedecampostecnologia@gmail.com";

    //Senha para autênticação do SMTP
    $mail->Password = "Fss&2812!";

    //Titulo do e-mail que será enviado
    $mail->Subject = $assunto;

    //Preenchimento do campo FROM do e-mail
    $mail->From = $campoEmail;
    $mail->FromName = $campoNome;
    $mail->addReplyTo($campoEmail, $campoNome);

    //E-mail para a qual o e-mail será enviado
    if (is_array($destinatario) && count($destinatario) > 1) {
        foreach ($destinatario as $keyDestinatario => $valueDestinatario) {
            if ($keyDestinatario > 0)
                $mail->AddCC($valueDestinatario);
            else
                $mail->AddAddress($valueDestinatario);
        }
    } else {
        $mail->AddAddress($destinatario);
    }

    //Conteúdo do e-mail
    $mail->Body = $mensagem;
    $mail->AltBody = $mail->Body;

    //Dispara o e-mail
    $enviado = $mail->Send();

    if ($enviado !== TRUE) {
        echo '
            <script>
                alert("Erro ao tentar enviar o e-mail. \n\nPor favor tente novamente mais tarde.");
    			window.history.back(-1);
            </script>
        ';
    } else {
        echo '
            <script>
                alert("Sua mensagem foi enviada com sucesso!");
                window.location.href="http://www.bateriasjacana.com.br/";
            </script>
        ';
    }
} catch (Exception $e) {
    echo '
            <script>
                alert("Erro ao tentar enviar o e-mail. \n\nPor favor tente novamente mais tarde. Mailer Error: '.$mail->ErrorInfo.'");
                window.history.back(-1);
            </script>
        ';
}
