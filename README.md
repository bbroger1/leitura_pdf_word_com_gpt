# SapiensRAG
Código feito inicialmente para uso pessoal.
O código não está bonito, mas está funcional.


# Projeto
A ideia de SapiensRAG é facilitar interagir com documentos como PDF, txt, DOC e outros permitindo o usuário conversar com PDF
e outros arquivos.

# Ferramentas

Para usar esse projeto você vai precisar de uma chave de API de alguns dos serviços de IA de pelo menos uma dessas empresas:
OpenAI, Google (Gemini), Anthropic (Claude), Cohere (Obrigatório), ou algum modelo open source da Groq https://console.groq.com/playground

Google, Cohere e Groq tem planos gratuitos (free tier)

Chave api da Cohere é obrigatório no arquivo config.php pois o modelo command-r-plus será usado para gerar melhores termos
de buscas.

Crie sua conta grátis e pegue sua chave de API em https://dashboard.cohere.com/api-keys

# Como usar
Na página inicial deste projeto rode o ```composer update ```

Verifique se já tem pdftotext instaldo, com ```which pdftotext``` caso não tenha, instale com o comando abaixo.

No Linux (Debian/Ubuntu) poderá rodar: ```apt-get install poppler-utils```

No RedHat, CentOS, Rocky Linux ou Fedora use isso: ```yum install poppler-utils```

Pelo navegador, acesse dash.php dentro da pasta HTML e configure pelo menos um modelo de IA.

Preencha também as informações para Vectara.

## No arquivo config.php
No arquivo config.php defina as informações de acesso à base de dados.

Importe o SQL anexado a esse repositório.


# Vectara

Vectara será o banco de dados de Vetores usado para armazenar os arquivos enviados que será usado como contexto pela IA

Você pode criar uma conta gratuita aqui: https://vectara.com/

# OpenSource
Esse projeto usa https://github.com/ChatGPTNextWeb/ChatGPT-Next-Web como interface do chat com algumas alterações.

