## Запуск проекта

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose run --rm node npm install
docker compose run --rm node npm run scss:build
docker compose exec php php database/seed.php