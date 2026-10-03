# stcUfsm
S.T.C. (Sistema de Transporte Cooperativo) — UFSM/FW: Aplicativo de gestão de caronas para o trajeto entre a UFSM/FW e o Centro (e vice-versa).

Guia de Instalação e Configuração:

Banco de Dados: Importe o arquivo .sql fornecido para criar a estrutura de tabelas no seu servidor de banco de dados e atualize os dados de conexão com o banco (localhost, usuário, senha e nome da base) nos arquivos de configuração, especialmente no conexao.php, config.php e demais arquivos que realizam consultas.

Acesso Local: Após configurar a base de dados e os arquivos de conexão, acesse o sistema através do seu ambiente local em http://localhost/caronas.

Integração com o Mercado Pago: Abra os arquivos gerar_pix.php e gerar_cartao.php e substitua as credenciais de API e os dados de configuração pelas suas chaves oficiais da sua conta do Mercado Pago.

Configuração do Webhook: Configure a página webhook.php no painel do Mercado Pago como o endpoint oficial para receber as notificações de pagamento (Webhooks) da plataforma.

Automação de Limpeza (Cron Job): Para manter o sistema limpo e remover automaticamente as caronas de dias anteriores, configure um Cron Job no seu servidor para acessar periodicamente a URL https://carlitoslocacoes.com/caronas/limpar_caronas.php?token=stc_limpeza_segura_2026. Dica de operação: programe o cron job para rodar em um horário específico (geralmente na madrugada). Lembre-se de que, após esse horário de limpeza, as novas caronas do dia deverão ser reinseridas no sistema.

<img width="759" height="501" alt="image" src="https://github.com/user-attachments/assets/fc354597-39c7-45f1-b493-210f0dc28c35" />
