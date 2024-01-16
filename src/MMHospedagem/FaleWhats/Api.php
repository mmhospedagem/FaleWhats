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

        self::log("[JSON][API][VERIFICAÇÃO DE NÚMERO]: " . json_encode($send));

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

        self::log("[JSON][API][SIMULAR PRESENÇA]: " . json_encode($send));

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
                self::log("[JSON][API][SEND MSG]: " . json_encode($send));
                return $send;
            }
        }
    }

}
