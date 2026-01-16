**NB:** If using WSL2, it's better to have this repository in the Linux filesystem. This cuts down response time from 5s to 1s.

# Setup steps:
1. After cloning repository, copy values from .env.example to .env;
2. Use docker compose: `docker-compose up -d` to start the services;
3. Generate keys `docker exec -it latvenergo-app php artisan key:generate`;
4. Run migrations `docker exec -it latvenergo-app php artisan migrate`;
5. If you get the default Laravel page when opening http://localhost/, the server is working;

## Info par uzdevumu:
Izstrādāt noliktavas sistēmas DB un API ar Laravel 12:
Ir vairāki produkti, kuriem ir nosaukums, mazs apraksts, cena un pieejamais daudzums (quantity). Papildināt produktus ar seederi.
Ir pasūtījumi (orders), kas sastāv no vairākiem produktiem.
Kad tiek izveidots jauns pasūtījums, no katra produkta tiek atņemts pasūtītais daudzums.
Ja kādam produktam nav pieejams pasūtījuma daudzums, pasūtījums nedrīkst tikt izveidots.

## Papildu (kā bonusu novērtēsim):
Ja tiks uzrakstīti arī testi.
Vēlams commitus github pushot pēc iespējas biežāk.

## Rezultāts:
Veikto uzdevumu gaidīšu e-pastā github link-ā,
līdz 1dienai, 19.01.2026., plkst. 13.00.


# Plāns pa soļiem

1. ~~Ieinstalēt Laravel 12 (varbūt vērts paskatīties neoficiālos headless starter kitus, bet tas gan jau par daudz laika aizņemtu);~~
2. ~~Dokerizēt DB un app (nokopēt no kāda cita projekta);~~
3. Ieinstalēt Sanctum priekš API;
4. Sākt ar migrācijām (produkti, pasūtījumi);
5. Uztaisīt seederus priekš DB;
6. Implementēt kontrolieru loģiku produktiem (produktu saraksts ar daudzumu);
7. Implementēt kontrolieru loģiku pasūtījumiem (pārbaude, vai pietiekami daudz produktu; atgriež kļūmi, ja nav produkta daudzuma);
8. Uztaisīt requestus ar useBruno, lai pārbaudītājam būtu viegli pārbaudīt, jo viss saglabājas gitā;
9. Uzrakstīt testus;
10. Dokumentēt nepieciešamo palaišanai;

## Lietas, uz kurām gan jau skatīsies:
1) Vai palaist projektu ir vienkārši un ir instrukcijas (neaizmirsti apdeitot .env.example);
2) Vai tiek pielietots pagination, piemēram, produktu sarakstam;
3) Kā tiek formatēts/normalizēts pasūtījums datubāzē;
4) Vai tiek uzrakstīti testi, un kas tiek pārbaudīts (vajadzētu pārbaudīt, kas notiek, ja ir pasūtījums ar QTY lielāku par produkta pieejamību, tukšs pasūtījums, parasts pasūtījums, vai produktu saraksts kko atgriež);
