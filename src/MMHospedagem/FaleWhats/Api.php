<?php
namespace MMHospedagem\FaleWhats;
class Api extends FaleWhats {

    private function send($method,$resource,$request = []) {

        $endpoint = $this->url . $resource;
        $headers = [
            "Cache-Control: no-cache",
            "Content-type: application/json"
        ];

        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL 			=> 	$endpoint,
            CURLOPT_RETURNTRANSFER 	=> 	true,
            CURLOPT_CUSTOMREQUEST 	=> 	$method,
            CURLOPT_HTTPHEADER 		=> 	$headers,
            CURLOPT_SSL_VERIFYHOST  =>  false,
            CURLOPT_SSL_VERIFYPEER  =>  false
        ]);
        switch ($method) {
            case "POST":
            case "PUT":
                curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($request));
                break;
        }
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response,true);

    }

    public function validar_numero($telefone) {

        $request    =   [
            "numero"   =>  $telefone
        ];
        $send = self::send("POST","/{$this->id_sessao}/rest/consultas/{$this->licenca}/numero",$request);
        if ($this->log){
            self::log("[JSON][API][VERIFICAÇÃO DE NÚMERO]: " . json_encode($send));
        }
        return $send;


    }

    public function simular_presenca($telefone,$tipo) {

        $Numero_Recebedio   =   preg_replace('/\D/', '', $telefone);
        $validacaoNumero    =   self::validar_numero($Numero_Recebedio);
        $request    =   [
            "numero"   =>  $telefone,
            "presenca"  =>  $tipo
        ];
        $send = self::send("POST","/{$this->id_sessao}/rest/acoes/{$this->licenca}/presenca",$request);
        if ($this->log){
            self::log("[JSON][API][SIMULANDO PRESENÇA]: " . json_encode($send));
        }
        return $send;

    }

    public function send_texto($telefone,$texto) {

        $Numero_Recebedio = preg_replace('/\D/', '', $telefone);
        $validacaoNumero = self::validar_numero($Numero_Recebedio);
        if(($validacaoNumero["exists"] == true)) {
            if((strlen($Numero_Recebedio) < 5)) {
                return "[ERROR][{$Numero_Recebedio}] Não foi possivel enviar sua mensagem número de telefone invalido.";
            } else {
                $request    =   [
                    "messageData"   =>  [
                        "numero"    =>  $validacaoNumero["jid"],
                        "text"  =>  html_entity_decode($texto)
                    ]
                ];
                if(($this->simular_presenca == true)) {
                    self::simular_presenca($validacaoNumero["jid"],'composing');
                }
                $send = self::send("POST","/{$this->id_sessao}/rest/envio/{$this->licenca}/texto",$request);
                if ($this->log){
                    self::log("[JSON][API][ENVIO DE MENSAGEM TIPO TEXTO]: " . json_encode($send));
                }
                return $send;
            }
        }
    }

    public function log($mensagem, $arquivo = null) {

        $diretorio = dirname(__FILE__) . DIRECTORY_SEPARATOR ."Logs";
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
        if ($arquivo === null) {
            $arquivo = $diretorio . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
        } else {
            $arquivo = $diretorio . DIRECTORY_SEPARATOR . $arquivo;
            $arquivoDiretorio = pathinfo($arquivo, PATHINFO_DIRNAME);
            if (!is_dir($arquivoDiretorio)) {
                mkdir($arquivoDiretorio, 0777, true);
            }
        }
        $dataHora = date('Y-m-d H:i:s');
        $mensagemFormatada = "[$dataHora] $mensagem" . PHP_EOL;
        $handle = fopen($arquivo, 'a');
        fwrite($handle, $mensagemFormatada);
        fclose($handle);

    }

    public function viewLog($data) {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
            list($dia, $mes, $ano) = explode('/', $data);
            $data = "$ano-$mes-$dia";
        }
        $diretorio = dirname(__FILE__) . DIRECTORY_SEPARATOR . "Logs";
        $arquivo = $diretorio . DIRECTORY_SEPARATOR . $data . '.log';
        if (file_exists($arquivo)) {
            $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            return implode("<br>", $linhas);
        }
        return "O arquivo de log para a data especificada não foi encontrado.";
    }

    public function fullLog($diretorio = 'Logs', $formato = 'Y-m-d') {
        $caminhoCompleto = dirname(__FILE__) . DIRECTORY_SEPARATOR . $diretorio . DIRECTORY_SEPARATOR;
        $arquivos = glob($caminhoCompleto . '*.log');

        // Função de comparação personalizada para ordenar os arquivos pela data no nome
        usort($arquivos, function ($a, $b) {
            $dataA = pathinfo($a, PATHINFO_FILENAME);
            $dataB = pathinfo($b, PATHINFO_FILENAME);
            return strtotime($dataA) - strtotime($dataB);
        });
        $conteudoCompleto = '';
        foreach ($arquivos as $arquivo) {
            $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $conteudoArquivo = implode("\n", $linhas);
            $conteudoCompleto .= '<pre>' . $conteudoArquivo . '</pre>' . PHP_EOL;
        }
        return $conteudoCompleto;
    }
}
