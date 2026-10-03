# stcUfsm
Aplicativo de carona UFSM/FW para Centro, Centro para UFSM/FW

basta criar o banco com o .sql, e atualizar com o localhost e config de db nos conexao.php e config.php e nos outros arquivos, e dai acessar localhost/caronas
alterar o gerar_pix.php gerar_cartao.php com os dados do mercadopago seus, visualizar webhook.php botar como cronjob no mercado pago o webhook page.

criar cronjob de https://carlitoslocacoes.com/caronas/limpar_caronas.php?token=stc_limpeza_segura_2026 para horario que o sistema vai limpar as caronas dos dias anteriores, e atual. assim tendo que fazer a insercao das caronas depois desse horario.
