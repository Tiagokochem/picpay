# PicPay Backend Challenge

Este projeto implementa uma API RESTful inspirada no PicPay, com foco em transferências seguras entre usuários. Desenvolvido em Laravel 12 com Laravel Sail, seguindo boas práticas de arquitetura e testes.

---

## ⚙️ Tecnologias

- PHP 8.3
- Laravel 12
- Laravel Sail (Docker)
- MySQL
- Guzzle HTTP Client
- PHPUnit

---

## 🚀 Como executar localmente

```bash
git clone https://github.com/Tiagokochem/picpay.git
cd picpay


docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php83-composer:latest \
  composer install --ignore-platform-reqs


./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

---

## 🔐 Regras de negócio

- Dois tipos de usuários: `common` e `shopkeeper`
- Apenas usuários `common` podem realizar transferências
- Verificação de saldo antes da transferência
- Autorização externa via API mock
- Transação deve ser atômica (com rollback em falhas)
- Notificação enviada ao recebedor após transferência (via API mock)

---

## 📡 Endpoint principal

### `POST /api/transfer`

#### Corpo da requisição (JSON):

```json
{
  "value": 100.0,
  "payer": 1,
  "payee": 2
}
```

#### Regras de validação:
- `payer` e `payee` devem ser usuários válidos e distintos
- `value` deve ser maior que zero

---

## 👥 Seeders de exemplo

O seeder cria usuários com diferentes saldos e tipos:

| ID | Nome        | Tipo        | Saldo   |
|----|-------------|-------------|---------|
| 1  | Usuário 1   | common      | 1000.00 |
| 2  | Usuário 2   | common      | 200.00  |
| 3  | Usuário 3   | shopkeeper  | 0.00    |
| 4  | Usuário 4   | common      | 50.00   |

---

## ✅ Testes

Para executar os testes automatizados:

```bash
./vendor/bin/sail test
```

Os testes cobrem:

✅ Transferência autorizada

Teste: user can transfer money

✅ Saldo insuficiente

Teste: transfer fails with insufficient balance

✅ Lojista tentando transferir (negado)

Teste: shopkeeper cannot initiate transfer

✅ Rejeição por API externa (autorização negada)

Teste: transfer fails when authorization is denied

✅ Validação de campos obrigatórios

Teste: transfer fails with invalid fields

✅ Job de notificação é dispatchado com sucesso

Teste: transfer dispatches notification job

- ![image](https://github.com/user-attachments/assets/9a41e9e0-c92b-44b0-84fc-702d0ec07d3a)


---

## 📌 Notas

- Mock de autorização: `https://util.devi.tools/api/v2/authorize`
- Mock de notificação: `https://util.devi.tools/api/v1/notify`
- Todos os erros são retornados com status HTTP apropriado (`400`, `403`, etc)

---

## 💡 Melhorias futuras

- Autenticação com Sanctum
- Versionamento de API
- Filas com Redis
- Swagger (OpenAPI)

---

## 👨‍💻 Autor

Feito por [Tiago Kochem](https://www.linkedin.com/in/tiagokochem)
