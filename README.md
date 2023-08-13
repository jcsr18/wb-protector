## WB Protector
Serviço responsável por receber webhooks externos e distribuir entre outros projetos


## Receivers: Facilitando o Processamento de Webhooks

Os **receivers** desempenham um papel crucial no recebimento e redirecionamento de webhooks provenientes de serviços externos para sua solução. Ao utilizar esse recurso, você simplifica a integração de sistemas e permite o processamento eficiente de informações externas.

### Adicionando um Novo Receiver

Integrar um novo **receiver** em sua aplicação é um processo simples:

1.  **Crie uma Rota e um Controller:** Primeiramente, crie uma rota que direcionará os webhooks recebidos para um controller específico. Isso garante que os dados sejam devidamente processados.

2.  **Registre a URL do Serviço Externo:** No arquivo `config/autoload/receivers.php`, registre a URL do serviço externo do qual você espera receber webhooks. Isso permitirá que seu sistema saiba para onde direcionar os dados recebidos.

3.  **Crie um Receiver Personalizado:** Crie uma classe que herde `App\Receiver\AbstractReceiver`.

   -   **Preencha a Propriedade `provider`:** Na classe do seu receiver, preencha a propriedade `public string $provider` com o nome do provedor externo. Isso ajuda a identificar a origem do webhook.

### Gerenciando Webhooks com Falha

1.  O webhook é registrado na base de dados com o status `false`, indicando que houve uma falha no envio.

2.  A tarefa `app/Task/RetryFailedWebhookTask.php` é acionada para lidar com os webhooks que falharam. Ela tenta reenviar esses webhooks para garantir que as informações sejam processadas corretamente.

3.  Enquanto o status do webhook permanecer como `false`, a tarefa de reenvio será agendada de acordo com a configuração do cron definida na mesma tarefa. Isso garante que os webhooks sejam reprocessados em intervalos regulares até que a transmissão bem-sucedida seja confirmada pelo sistema receptor.