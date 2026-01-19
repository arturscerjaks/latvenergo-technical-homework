**NB:** If using WSL2, it's better to have this repository in the Linux filesystem. This cuts down response time from 5s to 1s.

# Setup steps:
1. After cloning repository, copy values from .env.example to .env;
2. Use docker compose: `docker-compose up -d` to start the services;
3. Generate keys `docker exec -it latvenergo-app php artisan key:generate`;
4. Run migrations `docker exec -it latvenergo-app php artisan migrate`;
5. If you get the default Laravel page when opening http://localhost/, the server is working;
6. Seed the database `docker exec -it latvenergo-app php artisan db:seed`;
7. Run tests `docker exec -it latvenergo-app php artisan test`

I prepared a workspace with the API testing tool UseBruno, you can use it to quickly test the API routes.

If you are not familiar with it, UseBruno is very similar to Postman, but entirely local and everything
is stored in files which means it can be commited to git.
All you need to do is install it, and open the .usebruno/latvenergo-homework workspace through it,
then you should see a nice collection of requests (auth, products and orders).
