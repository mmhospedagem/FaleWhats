<?php
namespace MMHospedagem\FaleWhats;

class FaleWhats {
    protected $url;
    protected $licenca;
    protected $id_sessao;
    protected $simular_presenca;

    protected $log;

    public function __construct($url, $licenca, $id_sessao, $simular_presenca, $log) {
        $this->url = $url;
        $this->licenca = $licenca;
        $this->id_sessao = $id_sessao;
        $this->simular_presenca = $simular_presenca;
        $this->log = $log;
    }
}
